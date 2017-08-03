/**
 * Created by wrauk on 02.08.17.
 */


// var cTagContainer = {};

var tokenTagger = {};

$(function () {
    CTag = function CTag(name, abbr, categories){
        this.name = name;
        this.abbr = abbr;
        this.categories = categories;

        this.setCnt = 0; // setting to -1, first value is class
        this.chosenValues = [];
        this.allValuesChosen = false;
    };

    CTag.prototype.data= [];
    CTag.prototype.data.attributes = {};
    CTag.prototype.data.attributes.number=['sg', 'pl'];
    CTag.prototype.data.attributes.case=['nom', 'gen', 'dat', 'acc', 'inst', 'loc', 'voc'];
    CTag.prototype.data.attributes.gender=['m1', 'm2', 'm3', 'f', 'n'];
    CTag.prototype.data.attributes.person=['pri', 'sec', 'ter'];
    CTag.prototype.data.attributes.degree=['pos', 'com', 'sup'];
    CTag.prototype.data.attributes.aspect=['imperf', 'perf'];
    CTag.prototype.data.attributes.negation=['aff', 'neg'];
    CTag.prototype.data.attributes.accommodability=['congr', 'rec'];
    CTag.prototype.data.attributes.accentability=['akc', 'nakc'];
    CTag.prototype.data.attributes['post-prepositionality']=['npraep', 'praep'];
    CTag.prototype.data.attributes.agglutination=['agl', 'nagl'];
    CTag.prototype.data.attributes.vocalicity=['nwok', 'wok'];
    CTag.prototype.data.attributes.fullstoppedness=['pun', 'npun'];


    CTag.prototype.copy = function () {
      return new CTag(this.name, this.abbr, this.categories);
    };

    CTag.prototype.clear = function(){
        this.chosenValues = [];
        this.setCnt = 0;
    };

    CTag.prototype.setCurrentTag = function(val){
        console.log('setting tag: ', val, ', at position: ', this.setCnt);
        if(this.validateTag(this.setCnt, val)){
            this.chosenValues[this.setCnt++] = val;
            console.log('current set cnt: ', this.setCnt);
            return true;
        } else{
            //todo
            console.log('invalid tag');
            return false;
        }

    };

    CTag.prototype.validateTag = function(idx, tag){
        // return true;
        return this.data.attributes[this.categories[idx]].indexOf(tag) > -1;
    };

    CTag.prototype.getCurrentPossibleTags = function(){
        if(this.categories.length <= this.setCnt){
            return [];
        }
        return this.data.attributes[this.categories[this.setCnt]];
    };
    CTag.prototype.areAllValuesSet = function(){
        return this.categories.length <= this.setCnt;
    };

    function CTagContainer(){
        this.currentCTag = null;
        this.inputVal = null;
        this.init();
        this.initEditableSelect();
    }




    CTagContainer.prototype.init = function(){
        var self = this;
        self.data= {};
        self.data.classes=[];
        self.data.classes.push(new CTag('noun', 'subst', ['case','gender','person',]));
        self.data.classes.push(new CTag('depreciative form', 'depr', ['case','gender','person',]));
        self.data.classes.push(new CTag('main numeral', 'num', ['case','gender','person','agglutination',]));
        self.data.classes.push(new CTag('collective numeral', 'numcol', ['case','gender','person','agglutination',]));
        self.data.classes.push(new CTag('adjective', 'adj', ['case','gender','person','aspect',]));
        self.data.classes.push(new CTag('ad-adjectival adjective', 'adja', []));
        self.data.classes.push(new CTag('post-prepositional adjective', 'adjp', []));
        self.data.classes.push(new CTag('predicative adjective', 'adjc', []));
        self.data.classes.push(new CTag('adverb', 'adv', ['aspect',]));
        self.data.classes.push(new CTag('non-3rd person pronoun', 'ppron12', ['case','gender','person','degree','post-prep.',]));
        self.data.classes.push(new CTag('3rd-person pronoun', 'ppron3', ['case','gender','person','degree','post-prep.','accom.',]));
        self.data.classes.push(new CTag('pronoun siebie', 'siebie', ['gender',]));
        self.data.classes.push(new CTag('non-past form', 'fin', ['case','degree','negation',]));
        self.data.classes.push(new CTag('future być', 'bedzie', ['case','degree','negation',]));
        self.data.classes.push(new CTag('agglutinate być', 'aglt', ['case','degree','negation','fullstop.',]));
        self.data.classes.push(new CTag('l-participle', 'praet', ['case','person','negation','vocalicity',]));
        self.data.classes.push(new CTag('imperative', 'impt', ['case','degree','negation',]));
        self.data.classes.push(new CTag('impersonal', 'imps', ['negation',]));
        self.data.classes.push(new CTag('infinitive', 'inf', ['negation',]));
        self.data.classes.push(new CTag('contemporary adv. participle', 'pcon', ['negation',]));
        self.data.classes.push(new CTag('anterior adv. participle', 'pant', ['negation',]));
        self.data.classes.push(new CTag('gerund', 'ger', ['case','gender','person','negation','accentability',]));
        self.data.classes.push(new CTag('active adj. participle', 'pact', ['case','gender','person','negation','accentability',]));
        self.data.classes.push(new CTag('passive adj. participle', 'ppas', ['case','gender','person','negation','accentability',]));
        self.data.classes.push(new CTag('winien', 'winien', ['case','person','negation',]));
        self.data.classes.push(new CTag('predicative', 'pred', []));
        self.data.classes.push(new CTag('preposition', 'prep', ['gender',]));
        self.data.classes.push(new CTag('coordinating conjunction', 'conj', []));
        self.data.classes.push(new CTag('subordinating conjunction', 'comp', []));
        self.data.classes.push(new CTag('particle-adverb', 'qub', []));
        self.data.classes.push(new CTag('abbreviation', 'brev', []));
        self.data.classes.push(new CTag('bound word', 'burk', []));
        self.data.classes.push(new CTag('interjection', 'interj', []));
        self.data.classes.push(new CTag('punctuation', 'interp', []));
        self.data.classes.push(new CTag('alien', 'xxx', []));
        self.data.classes.push(new CTag('unknown form', 'ign', []));
    };

    /**
     * @constructor
     * @param {string} abbr - cTag abbreviation
     * @returns {CTag|undefined} cTag - searched cTag
     */
    CTagContainer.prototype.getCategoryByAbbr = function(abbr){
        console.log(abbr);
        var ret = this.data.classes.find(function(c){
            return c.abbr === abbr;
        });
        // console.log(ret);
        return ret.copy();
    };

    /**
     * @constructor
     * @param {string} name - cTag name
     * @returns {CTag|undefined} cTag - searched cTag
     */
    CTagContainer.prototype.getCategoryByName = function(name){
        return this.data.classes.find(function(c){
            return c.name === name;
        }).copy();
    };

    CTagContainer.prototype.isLastTag = function(){
        var self = this;
        return self.currentCTag.setCnt > self.currentCTag.categories.length -1;
    };

    CTagContainer.prototype.showNextPossibleTags = function(){
        var self = this;
        if(self.currentCTag.setCnt < self.currentCTag.categories.length) {
            var nextPossibleTags = self.currentCTag.getCurrentPossibleTags();
            console.log('possible tags:', nextPossibleTags);
            if (nextPossibleTags.length === 0)
                return false; // nothing more to show

            for (var i = 0; i < nextPossibleTags.length; i++) {
                console.log(self.inputVal);
                self.editableSelectHandle.editableSelect('add', self.inputVal + nextPossibleTags[i]);
            }
            self.editableSelectHandle.editableSelect('show');
            return true;
        }
        return false;
    };

    CTagContainer.prototype.clear = function(){
        // todo
    };

    /*
     * Handling input
     */

    CTagContainer.prototype.addColonAtInputEndIfAbsent = function(){
        var self = this;
        if(!self.inputVal.endsWith(':')){
            this.inputVal += ':';

            // adding necessary option
            self.editableSelectHandle.editableSelect('add', self.inputVal + ':');

            // finding that option
            var children = self.editableSelectHandle.next().children();
            var lastChild = $(children[children.length-1]);

            $(children).removeClass('selected');
            lastChild.add('selected');
            self.editableSelectHandle.val(self.inputVal);
            lastChild.css('display', 'none');

            // self.editableSelectHandle.editableSelect('select', lastChild);
        }
    };

    CTagContainer.prototype.initEditableSelect = function(){
        var self = this;
        self.editableSelectOriginalHandle = $('#ctag-select');
        self.editableSelectOriginalHandle.editableSelect({
            effects: 'slide',
            duration: 50
        });
        self.editableSelectHandle = $('#ctag-select'); // needed duplicate select

        self.editableSelectHandle.on('select.editable-select', function (e) {

            if(self.inputVal === e.target.value){
                self.editableSelectHandle.editableSelect('show');
                console.log('returning');
                return;
            }
            self.inputVal = e.target.value;

            var explodedTags = self.inputVal.split(":").filter(function(t){return t !== ''});
            console.log(explodedTags);
            if(explodedTags.length === 1){ // initializing element
                self.currentCTag = self.getCategoryByAbbr(explodedTags[0]);
            } else {

                var classes = explodedTags.slice(1);
                self.currentCTag.setCurrentTag(classes[classes.length -1 ]);

            }

            // checking if all values are set
            if(!self.currentCTag.areAllValuesSet()){
                self.addColonAtInputEndIfAbsent();
                self.showNextPossibleTags();

                setTimeout(function() {
                    self.editableSelectHandle.editableSelect('show');
                }, 150); // possible racing condition
            }
        });
    };

    CTagContainer.prototype.onInputTagChange = function(inputVal, event) {
        var self = this;
        self.inputVal = inputVal;
        // console.log(event);
        if(event.key === 'Backspace'){

        }
        else if (event.key === ':'){
            if(this.currentCTag) {
                var explodedTags = inputVal.split(":");
                if(explodedTags.length > 1){
                    explodedTags = explodedTags.filter(function(t){return t !== '';});
                    self.currentCTag.setCurrentTag(explodedTags[explodedTags.length-1]);
                    self.showNextPossibleTags();
                    console.log(inputVal.split(':'));
                }
            } else{
                self.currentCTag = self.getCategoryByAbbr(inputVal.replace(':',''));
                self.showNextPossibleTags();
            }
        }
        self.editableSelectHandle.editableSelect('show');
    };

    cTagContainer = new CTagContainer();


    // console.log(ctagContainer);
    // var tag = ctagContainer.getCategoryByAbbr('subst');
    // console.log(tag);
    // console.log(tag.getCurrentPossibleTags());
    // console.log(tag.setCurrentTag("dat"));
    // console.log(tag.getCurrentPossibleTags());
    // console.log(tag.setCurrentTag("m1"));
    // console.log(tag.getCurrentPossibleTags());
    // console.log(tag.setCurrentTag("sec"));
    // console.log(tag.getCurrentPossibleTags());

    function TokenTagger(handle){
        this.handle = handle;
        this.cTagCont = new CTagContainer();
        this.init();
    }

    TokenTagger.prototype.onInputTagChange = function(value, event){
        this.cTagCont.onInputTagChange(value, event);
    };

    TokenTagger.prototype.init = function(){
        this.useArrowKeys = false;
        this.chunks = this.handle.find('span.token');
        // this.currentChunk = $(this.chunks[0]);
        // this.currentChunkIdx = 0;

        this.annotatedWordHandle = $('#anotated-word');
        this.setCurrentToken(0);
    };

    TokenTagger.prototype.turnOnArrowKeys = function(){
        this.useArrowKeys = true;
        this.prevDocumentOnKeyDown = document.onkeydown;
        document.onkeydown = this.handleKeyDown.bind(this);
    };

    TokenTagger.prototype.turnOffArrowKeys = function(){
        this.useArrowKeys = false;
        document.onkeydown = this.prevDocumentOnKeyDown || document.onkeydown;
    };


    /**
     * @param {number | object} element - chunk index or chunk jquery object
     */
    TokenTagger.prototype.setCurrentToken = function(element){
        var self = this;

        // todo - save current word setting if correct
        // self.cTagCont.saveCurrent();
        self.cTagCont.clear();

        if(typeof(element) === 'number'){ // element is index
            self.currentChunkIdx = element;
            element = $(self.chunks[element]);
        } else{
            // todo
            self.currentChunkIdx = self.chunks.indexOf(element[0]);
        }

        self.currentChunk = element;
        self.chunks.removeClass('token-tagger-active');
        self.currentChunk.addClass('token-tagger-active');
        self.annotatedWordHandle.text(self.currentChunk.text());
    };

    TokenTagger.prototype.nextToken = function(){
        var self = this;
        if(self.currentChunkIdx + 1 < self.chunks.length)
            self.setCurrentToken(self.currentChunkIdx + 1);
    };

    TokenTagger.prototype.prevToken = function(){
        var self = this;
        if(self.currentChunkIdx -1 >= 0 )
            self.setCurrentToken(self.currentChunkIdx -1);
    };

    TokenTagger.prototype.handleKeyDown = function(e){
        var self = this;

        if(e.key === "ArrowLeft"){
            e.preventDefault();
            self.prevToken();
        } else if (e.key === "ArrowRight"){
            e.preventDefault();
            self.nextToken();
        }
    };

    var tagger = new TokenTagger($('#chunklist'));
    tagger.turnOnArrowKeys();
    console.log(tagger.chunks);
    console.log(tagger.currentChunk);
});

