/**
 * MeicanI18N 1.0
 *
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 *
 * Requirements - javascripts load order:
 * 1. This file
 * 2. Translations sources
 * 3. Your scripts
 *
 * Translation sources:
 * I18N.begin("pt-BR");
 * I18N.add("word", "palavra");
 *
 * Usage in your scripts:
 * I18N.init("pt-BR"); 
 * I18N.t("word");
 * ...
 * I18N.t("word");
 *
 */

var I18N = new MeicanI18N;

function MeicanI18N() {
    this._dic = [];
    this._declareLang;
    this._activeLang = language;
}

MeicanI18N.prototype.init = function(lang) {
    this._activeLang = lang;
}

MeicanI18N.prototype.begin = function(lang) {
    if(!this._dic[lang]) this._dic[lang] = [];
    this._declareLang = lang;
}

MeicanI18N.prototype.add = function(key, value) {
    this._dic[this._declareLang][key] = value;
}

MeicanI18N.prototype.t = function(key) {
    if(this._activeLang && 
            this._dic && 
            this._dic[this._activeLang] 
            && this._dic[this._activeLang][key]) {
        return this._dic[this._activeLang][key];
    } else return key;
} 
