<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\nsi;

use Yii;
use yii\helpers\Url;

/**
 * Classe que implementa o módulo SoapClient do protocolo NSI Connection Service Requester 2.0
 * 
 * Envia mensagens para provedores NSI para criar, alterar ou remover conexões (circuitos).
 *
 * @author Maurício Quatrin Guerreiro
 */
class ConnectionRequesterClient extends \SoapClient {
    
    private $requesterURL;
    private $providerNSA;
    private $requesterNSA;
    private $version;

    function __construct($requesterNSA, $requesterURL, $providerNSA, $providerURL, $certificatePath, 
            $certificatePass) {
        $providerWSDL = Url::toRoute("/circuits/nsi/wsdl/ogf_nsi_connection_provider_v2_0.wsdl", "http");
        $this->requesterURL = $requesterURL;
        $this->providerNSA = $providerNSA;
        $this->requesterNSA = $requesterNSA;

        $soapOptions = array(
            "local_cert" => $certificatePath,
            "passphrase" => $certificatePass,
            "cache_wsdl" => WSDL_CACHE_NONE,
            "trace" => 1
        );
        
        parent::__construct($providerWSDL, $soapOptions);
    }

    function __doRequest($request, $location, $action, $version, $one_way = 0) {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($request);
        Yii::trace($request);
        
        /** Setting namespaces **/
        $dom->documentElement->setAttribute('xmlns:xs', 'http://www.w3.org/2001/XMLSchema');
        $dom->documentElement->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $dom->documentElement->setAttribute('xmlns:gns', 'http://nordu.net/namespaces/2013/12/gnsbod');
        $dom->documentElement->setAttribute('xmlns:saml', 'urn:oasis:names:tc:SAML:2.0:assertion');
        //$dom->documentElement->setAttribute('xmlns:type', 'http://schemas.ogf.org/nsi/2013/12/connection/types');
        //$dom->documentElement->setAttribute('xmlns:head', 'http://schemas.ogf.org/nsi/2013/12/framework/headers');
        
        /** Generating Correlation ID */
        $dom->getElementsByTagName("correlationId")->item(0)->nodeValue = 'urn:uuid:'.$this->newGuid();
        
        /** Setting prefixes of the elements **/
        $this->changeTag($dom, "ConnectionTrace", "gns:ConnectionTrace");
        $this->changeTag($dom, "reserve", "ns1:reserve");
        $this->changeTag($dom, "reserveCommit","ns1:reserveCommit");
        $this->changeTag($dom, "provision", "ns1:provision");
        $this->changeTag($dom, "p2ps", "p2p:p2ps");
        $this->changeTag($dom, "Attribute", "saml:Attribute");
        $this->changeTag($dom, "AttributeValue", "saml:AttributeValue");
        $this->changeTag($dom, "Attribute", "saml:Attribute");
        $this->changeTag($dom, "AttributeValue", "saml:AttributeValue");

        /** Setting attributes **/
        $this->setSecurityAttributes($dom);
        $this->setAttributeByTag($dom, "saml:AttributeValue", "xsi:type", "xs:string");
        $this->setAttributeByTag($dom, "Connection", "index", "0");
        $this->setAttributeByTag($dom, "criteria", "version", $this->version);
        $this->setAttributeByTag($dom, "p2p:p2ps", "xmlns:p2p", "http://schemas.ogf.org/nsi/2013/12/services/point2point");
        $this->setAttributeByTag($dom, "parameter", "type", "protection");
        $this->setEro($dom);
        $request = $dom->saveXML();
        Yii::trace($request);
        
        return parent::__doRequest($request, $location, $action, $version);
    }

    function changeTag($dom, $oldTagName, $newTagName, $attributes = array()){
        $newNode = $dom->createElement($newTagName);

        if($oldNode = $dom->getElementsByTagName($oldTagName)->item(0)){

            $childNodes = $oldNode->childNodes;
            foreach($childNodes as $child){
                $newChild = $child->cloneNode(true);
                $newNode->appendChild($newChild);
            }

            $parent = $oldNode->parentNode;
            $parent->replaceChild($newNode, $oldNode);
        }
    }

    function setAttributeByTag($dom, $tagName, $attName, $attValue){
        if($nodes = $dom->getElementsByTagName($tagName)){
            foreach($nodes as $node)
                $node->setAttribute($attName, $attValue);
        }
    }

    function setSecurityAttributes($dom){
        if($nodes = $dom->getElementsByTagName("saml:Attribute")){
            foreach($nodes as $node) {
                $node->setAttribute('Name', $node->nodeValue);
                if ($node->firstChild) {
                    $node->firstChild->nodeValue = Yii::$app->session->get('auth_'.$node->nodeValue);
                }
            }
        }
    }
    
    function setEro($dom){
        if($nodes = $dom->getElementsByTagName("orderedSTP")){
            $i = 0;
            foreach($nodes as $node) {
                $node->setAttribute("order", $i);
                $i++;
            }
        }
    }

    function setAggHeader(){
        $ns = "http://schemas.ogf.org/nsi/2013/12/framework/headers";
        $connection = new \SoapVar(array("Connection" => $this->requesterNSA), 
            SOAP_ENC_OBJECT, null, null, null, null);
        $attributes = array();
        $attributes[] = new \SoapVar(array("AttributeValue"=>"user"), 
            SOAP_ENC_OBJECT, null, null, 'Attribute', null);
        $attributes[] = new \SoapVar(array("AttributeValue"=>"token"), 
            SOAP_ENC_OBJECT, null, null, 'Attribute', null);
        $securityAttr = new \SoapVar($attributes,
            SOAP_ENC_OBJECT, null, null, null, null);

//         <sessionSecurityAttr>
            //     <ns2:Attribute Name="user">
            //         <ns2:AttributeValue xsi:type="xs:string">bar
            //         </ns2:AttributeValue>
            //     </ns2:Attribute>
            //     <ns2:Attribute Name="token">
            //         <ns2:AttributeValue xsi:type="xs:string">0000
            //         </ns2:AttributeValue>
            //     </ns2:Attribute>
            // </sessionSecurityAttr>

        $headerBody = array(
            "protocolVersion"=>"application/vnd.ogf.nsi.cs.v2.provider+soap",
            "correlationId"  =>"", //Generated on request
            "requesterNSA"   => $this->requesterNSA,
            "providerNSA"    =>$this->providerNSA,
            "replyTo"       => $this->requesterURL,
            "sessionSecurityAttr" => $securityAttr,
            "ConnectionTrace" => $connection
        );

        $headerBody = new \SoapVar($headerBody, SOAP_ENC_OBJECT, NULL, NULL, NULL, NULL);
        $header = new \SoapHeader($ns, "nsiHeader", $headerBody);

        $this->__setSoapHeaders($header);
    }

    function newGuid() {
        $s = md5(uniqid(rand(),true));
        $guidText =
            substr($s,0,8) . '-' .
            substr($s,8,4) . '-' .
            substr($s,12,4). '-' .
            substr($s,16,4). '-' .
            substr($s,20);
        return $guidText;
    }

    /**
     * Requisita um circuito ao provedor especificado na construcao do
     * objeto.
     *
     * @param $connectionId String opcional, obrigatoria se for edição
     * @param $version Inteiro
     * @param $bandwidth Inteiro
     * @param $startTime DateTime
     * @param $endTime DateTime
     * @param $path Array de STPs
     * @param $description String
     * @param $globalReservationId String opcional
     */
    public function requestReserve($connectionId, $version, $bandwidth, $startTime, 
            $endTime, $path, $description, $globalReservationId = null) {

        $this->version = $version;
        $serviceType = "http://services.ogf.org/nsi/2013/12/descriptions/EVTS.A-GOLE";
        $directionality = "Bidirectional";
        $symmetricPath = "true";
        $parameter = "PROTECTED";
        $criteria = [];
        $criteria['serviceType'] = $serviceType;
        $schedule = [];
        $params = [];

        if ($connectionId != null) {
            $params["connectionId"] = $connectionId;
        } 

        $schedule = array(
            "startTime" => $startTime->format('Y-m-d\TH:i:s.000-00:00'),
            "endTime" => $endTime->format('Y-m-d\TH:i:s.000-00:00')
        );
            
        $pathSize = count($path);
        $waypoints = new \ArrayObject();
        
        if($pathSize > 2) {
            for ($i = 1; $i < ($pathSize - 1); $i++) {
                $stp = new \SoapVar(['stp'=>$path[$i]], SOAP_ENC_OBJECT, NULL, NULL, null, NULL);
                $orderedSTP = new \SoapVar($stp, SOAP_ENC_OBJECT, NULL, NULL, 'orderedSTP', NULL);
                $waypoints->append($orderedSTP);
            }
            
            $ero = new \SoapVar($waypoints, SOAP_ENC_OBJECT, NULL, NULL, "ero", NULL);
                
            $p2ps = array(
                    "capacity" => $bandwidth, 
                    "directionality" => $directionality, 
                    "symmetricPath" => $symmetricPath, 
                    "sourceSTP" => $path[0],  
                    "destSTP" => $path[$pathSize-1], 
                    "parameter" => $parameter, 
                    $ero
            );
        } else {
            $p2ps = array(
                    "capacity" => $bandwidth, 
                    "directionality" => $directionality, 
                    "symmetricPath" => $symmetricPath, 
                    "sourceSTP" => $path[0],  
                    "destSTP" => $path[$pathSize-1], 
                    "parameter" => $parameter,
            );
        }
    
        $schedule = new \SoapVar($schedule, SOAP_ENC_OBJECT, NULL, NULL, NULL, NULL);
        $p2ps = new \SoapVar($p2ps, SOAP_ENC_OBJECT, NULL, NULL, NULL, NULL);
    
        $criteria = array(
                "schedule" => $schedule,
                "serviceType" => $serviceType,
                "p2ps" => $p2ps
        );
    
        $criteria = new \SoapVar($criteria, SOAP_ENC_OBJECT, NULL, NULL, NULL, NULL);
    
        if ($globalReservationId)
            $params["globalReservationId"] = $globalReservationId;

        $params["description"] = $description;
        $params["criteria"] = $criteria;

        $this->setAggHeader();
        
        try{
            $this->reserve($params);
        }catch(\SoapFault $error){
            Yii::trace($error);
            return false;
        }

        return true;
    }
    
    public function requestTerminate($connectionId) {
        $params = array(
                "connectionId" => $connectionId
        );
         
        $this->setAggHeader();
        
        try{
            $this->terminate($params);
        }catch(\SoapFault $error){
            Yii::trace($error);
            return false;
        }
        return true;
    }
    
    public function requestSummary($connectionId) {
        $params = array(
                "connectionId" => $connectionId
        );
        
        $this->setAggHeader();
        
        try{
            $this->querySummary($params);
        }catch(\SoapFault $error){
            Yii::trace($error);
            return false;
        }
        return true;
        
    }
    
    public function requestCommit($connectionId) {
        $params = array(
                "connectionId" => $connectionId
        );
        
        $this->setAggHeader();
        
        try{
            $this->reserveCommit($params);
        }catch(\SoapFault $error){
            Yii::trace($error);
            return false;
        }
        return true;
    }
    
    public function requestProvision($connectionId) {
        $params = array(
                "connectionId" => $connectionId
        );
         
        $this->setAggHeader();
        
        try{
            $this->provision($params);
        }catch(\SoapFault $error){
            Yii::trace($error);
            return false;
        }
        return true;
    }

    public function requestRelease($connectionId) {
        $params = array(
                "connectionId" => $connectionId
        );
         
        $this->setAggHeader();
        
        try{
            $this->release($params);
        }catch(\SoapFault $error){
            Yii::trace($error);
            return false;
        }
        return true;
    }
}