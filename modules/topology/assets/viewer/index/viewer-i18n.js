var dict = [];
dict['pt-BR'] = [];

dict['pt-BR']['Network'] = 'Rede';

dict['pt-BR'][''] = '';

function tt(obj){
	if(language == 'pt-BR' && dict['pt-BR'][obj]) return dict['pt-BR'][obj];
	else return obj;
}