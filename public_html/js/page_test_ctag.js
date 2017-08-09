/**
 * Created by wrauk on 02.08.17.
 */

var tokenTagger = {};

$(function () {

    function Tooltip(appendToHandle){
        this.html = '';
        this.handle = $( "<div>" )
            .addClass('tooltip tooltip-page-tag')
            .appendTo(appendToHandle);
    }

    Tooltip.prototype.show = function(html){
        var self = this;
        self.html = html || self.html;
        self.handle
            .html(self.html)
            .css('opacity', 0.6);
        return self;
    };

    Tooltip.prototype.addLine = function(line){
        this.html += '<br>\n' + line;
        this.show();
        return this;
    };

    Tooltip.prototype.hide = function(){
        this.handle.css('opacity', 0);
        return this;
    };

    /**
     * Initializes Tag object
     * @param {string} name
     * @param {string} abbr
     * @param {string[]} categories - categories that need to specified for the tag
     * @constructor
     */
    function Tag(name, abbr, categories){
        this.name = name;
        this.abbr = abbr;
        this.categories = categories;

        this.setCnt = 0;
        this.chosenValues = [];

        this.error = false;
    }

    Tag.prototype.data= [];

    Tag.prototype.data.attributes = {
        number: ['sg', 'pl'],
        case: ['nom', 'gen', 'dat', 'acc', 'inst', 'loc', 'voc'],
        gender: ['m1', 'm2', 'm3', 'f', 'n'],
        person: ['pri', 'sec', 'ter'],
        degree: ['pos', 'com', 'sup'],
        aspect: ['imperf', 'perf'],
        negation: ['aff', 'neg'],
        accommodability: ['congr', 'rec'],
        accentability: ['akc', 'nakc'],
        'post-prepositionality': ['npraep', 'praep'],
        agglutination: ['agl', 'nagl'],
        vocalicity: ['nwok', 'wok'],
        fullstoppedness: ['pun', 'npun']
    };

    Tag.prototype.data.attributes_full = {
        number: ['singular', 'plural'],
        case: ['nominative','genitive','dative','accusative','instrumental','locative', 'vocative'],
        gender:['human masculine (virile)','animate masculine','inanimate masculine','feminine','neuter'],
        person: ['first','second','third'],
        degree: ['positive','comparative','superlative'],
        aspect: ['imperfective', 'perfective'],
        negation: ['affirmative', 'negative'],
        accommodability: ['agreeing', 'governing'],
        accentability: ['accented (strong)', 'non-accented (weak)'],
        'post-prepositionality': ['non-post-prepositional', 'post-prepositional'],
        agglutination: ['agglutinative', 'non-agglutinative'],
        vocalicity: ['non-vocalic', 'vocalic'],
        fullstoppedness: ['with full stop', 'without full stop']
    };

    Tag.prototype.copy = function () {
      return new Tag(this.name, this.abbr, this.categories);
    };

    Tag.prototype.clear = function(){
        this.chosenValues = [];
        this.setCnt = 0;
    };

    Tag.prototype.setTagAtPosition = function(val, pos){
        if(this.validateTag(pos, val)){
            this.chosenValues[pos] = val;
            self.error = false;
            return true;
        } else{
            this.error = true;
            return false;
        }
    };

    Tag.prototype.setCurrentTag = function(val){
        return this.setTagAtPosition(val, this.setCnt++);
    };

    Tag.prototype.assignTags = function(tags){
        var self = this;

        for(var i = 0; i < tags.length; i++){
            if(!self.setTagAtPosition(tags[i], i)){
                this.error = true;
                return false;
            }
        }
        self.error = false;
        self.setCnt = tags.length;
        return true;
    };

    Tag.prototype.validateTag = function(idx, tag){
        return this.data.attributes[this.categories[idx]].indexOf(tag) > -1;
    };

    Tag.prototype.getCurrentPossibleTags = function(){
        if(this.categories.length <= this.setCnt){
            return [];
        }
        return this.data.attributes[this.categories[this.setCnt]];
    };

    Tag.prototype.getCurrentPossibleTagsFull = function(){
        if(this.categories.length <= this.setCnt){
            return [];
        }
        return this.data.attributes_full[this.categories[this.setCnt]];
    };

    Tag.prototype.areAllValuesSet = function(){
        return this.categories.length === this.chosenValues.length;
    };

    /**
     * Initializes TagContainer module
     * @param {jQuery} selectHandle - jQuery object handle for select element
     * @param {jQuery} moduleHandle - jQuery object handle for module element
     * @constructor
     */
    function TagContainer(selectHandle, moduleHandle){
        this.currentTag = null;
        this.inputVal = null;

        this.selectHandle = selectHandle;
        this.moduleHandle = moduleHandle;

        this.init();
        this.initEditableSelect();
    }

    TagContainer.prototype.init = function(){
        var self = this;

        self.tooltip = new Tooltip(self.moduleHandle);

        self.data= {};
        self.data.classes=[];
        self.data.classes = [
            new Tag('noun', 'subst',                        ['number','case','gender']),
            new Tag('depreciative form', 'depr',            ['number','case','gender']),
            new Tag('main numeral', 'num',                  ['number', 'case','gender', 'accommodability']),
            new Tag('collective numeral', 'numcol',         ['number', 'case','gender', 'accommodability']),
            new Tag('adjective', 'adj',                     ['number','case','gender', 'degree']),
            new Tag('ad-adjectival adjective', 'adja',      []),
            new Tag('post-prepositional adjective', 'adjp', []),
            new Tag('predicative adjective', 'adjc',        []),
            new Tag('adverb', 'adv',                        ['degree']),
            new Tag('non-3rd person pronoun', 'ppron12',    ['number','case','gender','person', 'accentability']),
            new Tag('3rd-person pronoun', 'ppron3',         ['number','case','gender','person', 'accentability','post-prepositionality']),
            new Tag('pronoun siebie', 'siebie',             ['case']),
            new Tag('non-past form', 'fin',                 ['number','person','aspect']),
            new Tag('future być', 'bedzie',                 ['number','person','aspect']),
            new Tag('agglutinate być', 'aglt',              ['number','person','aspect', 'vocalicity']),
            new Tag('l-participle', 'praet',                ['number','gender','aspect','agglutination']),
            new Tag('imperative', 'impt',                   ['number','person','aspect']),
            new Tag('impersonal', 'imps',                   ['aspect']),
            new Tag('infinitive', 'inf',                    ['aspect']),
            new Tag('contemporary adv. participle', 'pcon', ['aspect']),
            new Tag('anterior adv. participle', 'pant',     ['aspect']),
            new Tag('gerund', 'ger',                        ['number','case','gender','aspect','negation']),
            new Tag('active adj. participle', 'pact',       ['number','case','gender','aspect','negation']),
            new Tag('passive adj. participle', 'ppas',      ['number','case','gender','aspect','negation']),
            new Tag('winien', 'winien',                     ['number','gender','aspect']),
            new Tag('predicative', 'pred',                  []),
            new Tag('preposition', 'prep',                  ['case']),
            new Tag('coordinating conjunction', 'conj',     []),
            new Tag('subordinating conjunction', 'comp',    []),
            new Tag('particle-adverb', 'qub',               []),
            new Tag('abbreviation', 'brev',                 ['fullstoppedness']),
            new Tag('bound word', 'burk',                   []),
            new Tag('interjection', 'interj',               []),
            new Tag('punctuation', 'interp',                []),
            new Tag('alien', 'xxx',                         []),
            new Tag('unknown form', 'ign',                  [])
        ];

        this.data.classesAbbr = [];
        for(var i = 0; i < this.data.classes.length; i++){
            this.data.classesAbbr.push(this.data.classes[i].abbr);
        }
    };

    /**
     * @param {string} abbr - Tag abbreviation
     * @returns {Tag|null} Tag - searched Tag
     */
    TagContainer.prototype.getCategoryByAbbr = function(abbr){
        var ret = this.data.classes.find(function(c){
            return c.abbr === abbr;
        });
        if(ret)
            return ret.copy();
        else return null;
    };

    /**
     * @param {string} name - Tag name
     * @returns {Tag|undefined} Tag - searched Tag
     */
    TagContainer.prototype.getCategoryByName = function(name){
        return this.data.classes.find(function(c){
            return c.name === name;
        }).copy();
    };

    TagContainer.prototype.isLastTag = function(){
        var self = this;
        return self.currentTag.setCnt > self.currentTag.categories.length -1;
    };

    TagContainer.prototype.showNextPossibleTags = function(){
        var self = this;
        if(self.currentTag.setCnt < self.currentTag.categories.length) {
            var nextPossibleTags = self.currentTag.getCurrentPossibleTags();
            var fullPossibleTags = self.currentTag.getCurrentPossibleTagsFull();

            if (nextPossibleTags.length === 0)
                return false; // nothing more to show

            var tooltipList = '<ul>';
            for (var i = 0; i < nextPossibleTags.length; i++) {
                self.editableSelectHandle.editableSelect('add', self.inputVal + nextPossibleTags[i]);
                tooltipList += '<li>' + fullPossibleTags[i] + '- ' + nextPossibleTags[i] + '</li>'
            }
            tooltipList += '</ul>';

            self.editableSelectHandle.editableSelect('show');
            self.tooltip
                .show('<h5><b>' + self.currentTag.name.charAt(0).toUpperCase() + self.currentTag.name.slice(1) + '</b></h5>')
                .addLine('<p>choosing tag for category <b>' + self.currentTag.categories[self.currentTag.setCnt] + '</b></p>' )
                .addLine(tooltipList);

            return true;
        }
        return false;
    };

    TagContainer.prototype.showInitialOptions = function () {
        var self = this;
        for(var i = 0; i < this.data.classesAbbr.length; i++){
            this.editableSelectHandle.editableSelect('add', this.data.classesAbbr[i]);
        }
    };

    TagContainer.prototype.addOptions = function(options, placeAtFront){
        var self = this;
        var i;

        placeAtFront = placeAtFront || false;

        if(placeAtFront){
            for(i = options.length - 1; i >= 0 ; i--){
                self.editableSelectHandle.editableSelect('add', options[i], 0);
            }
        } else {
            for(i = 0; i < options.length ; i++){
                self.editableSelectHandle.editableSelect('add', options[i]);
            }
        }
    };

    TagContainer.prototype.clear = function(){
        var self = this;
        self.inputVal = '';
        self.editableSelectHandle.val('');
        self.editableSelectHandle.editableSelect('clear');
    };

    TagContainer.prototype.showDropOptionsTimeout = function(timeout){
        timeout = timeout | 200;
        var self = this;
        setTimeout(function() {
            self.editableSelectHandle.editableSelect('show');
        }, timeout); // possible racing condition
    };

    /*
     * Handling input
     */

    TagContainer.prototype.addColonAtInputEndIfAbsent = function(){
        var self = this;

        if(!self.inputVal.endsWith(':')){
            this.inputVal += ':';

            // adding necessary option
            self.editableSelectHandle.editableSelect('add', self.inputVal);

            // finding that option
            var children = self.editableSelectHandle.next().children();
            var lastChild = $(children[children.length-1]);

            self.editableSelectHandle.editableSelect('select', lastChild);
            lastChild.remove();
        }
    };

    TagContainer.prototype.focus = function(){
        this.editableSelectHandle.focus();
    };

    TagContainer.prototype.initEditableSelect = function(){
        var self = this;
        self.editableSelectOriginalHandle = self.selectHandle;
        self.editableSelectOriginalHandle.editableSelect({
            effects: 'slide',
            duration: 50
        });
        self.editableSelectHandle = $(self.selectHandle.selector); // needed duplicate select

        self.showInitialOptions();

        self.editableSelectHandle.on('select.editable-select', function (e) {
            // return if input didn't change and selected class is not possible
            if(self.inputVal === e.target.value && self.data.classesAbbr.indexOf(self.inputVal) < 0){
                self.editableSelectHandle.editableSelect('show');
                return;
            }
            self.inputVal = e.target.value;

            var explodedTags = self.inputVal.split(":").filter(function(t){return t !== ''});
            if(explodedTags.length === 1){ // initializing element
                self.currentTag = self.getCategoryByAbbr(explodedTags[0]);
            } else {

                var classes = explodedTags.slice(1);
                self.currentTag.assignTags(classes);
            }

            // checking if all values are set
            if(!self.currentTag.areAllValuesSet()){
                self.addColonAtInputEndIfAbsent();
                self.showNextPossibleTags();

                self.showDropOptionsTimeout(150); // possible racing condition
            }
        });
    };

    TagContainer.prototype.onInputTagChange = function(inputVal, event) {
        var self = this;

        self.inputVal = inputVal;
        // console.log(event);
        if(event.key === 'Backspace'){

        }
        else if (event.key === ':'){
            if(this.currentTag) {
                var explodedTags = inputVal.split(":");
                if(explodedTags.length > 1){
                    explodedTags = explodedTags.filter(function(t){return t !== '';});
                    self.currentTag.assignTags(explodedTags.splice(1));
                    self.showNextPossibleTags();
                }
            } else{
                self.currentTag = self.getCategoryByAbbr(inputVal.replace(':',''));
                self.showNextPossibleTags();
            }
        }
        self.editableSelectHandle.editableSelect('show');
    };

    TagContainer.prototype.hideListTimeout = function(timeout){
        var self = this;
        timeout = timeout || 300;

        setTimeout(function(){
            self.editableSelectHandle.editableSelect('hide');
        }, timeout);
    };

    /**
     * Initializes TokenTaggerModule
     * @param {jQuery} moduleHandle - jQuery object handle for div containing module
     * @param {jQuery} chunksHandle - jQuery object handle for element containing tags
     * @constructor
     */
    function TokenTagger(moduleHandle, chunksHandle){
        this.moduleHandle = moduleHandle;
        this.chunksHandle = chunksHandle;

        this.tagCont = new TagContainer(this.moduleHandle.find("[mod-id='tag-select']"), this.moduleHandle);
        this.state = this.states.INVALID;
        this.init();

        this.assignOnInputTagChange();
    }

    TokenTagger.prototype.init = function(){
        this.useArrowKeys = false;
        this.chunks = this.chunksHandle.find('span.token');

        this.handles = this.assignJqueryHandles();

        this.initButtons();
        this.setCurrentToken(0);
        this.updateStatesView();
    };

    TokenTagger.prototype.assignOnInputTagChange = function(){
        var self = this;
        this.tagCont.editableSelectHandle.on('keyup select', function(){
            self.updateState();
            self.updateStatesView();
        });
    };

    TokenTagger.prototype.onInputTagKeyUp = function(value, event){
        this.tagCont.onInputTagChange(value, event);
    };

    TokenTagger.prototype.assignJqueryHandles = function(){
        return {
            stateIndicators: {
                ok: this.moduleHandle.find("[mod-id='state-ok']"),
                invalid: this.moduleHandle.find("[mod-id='state-invalid']"),
                error: this.moduleHandle.find("[mod-id='state-error']")
            },
            annotatedWordHandle: this.moduleHandle.find("[mod-id='anotated-word']"),
            saveButton: this.moduleHandle.find("[mod-id='button-save']"),
            directionBtns: {
                next: this.moduleHandle.find("[mod-id='button-next']"),
                prev: this.moduleHandle.find("[mod-id='button-prev']")
            }
        }
    };

    TokenTagger.prototype.initButtons = function(){
        var self = this;
        self.handles.directionBtns.next.on('click', function () {
            self.nextToken();
        });

        self.handles.directionBtns.prev.on('click', function () {
            self.prevToken();
        });

        self.handles.saveButton.on('click', function() {
            console.log(getSelection());
            console.log('saving: ', self.tagCont.currentTag, 'token: ', self.chunks[self.currentChunkIdx]);
        });
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
        // self.tagCont.saveCurrent();
        self.tagCont.clear();
        self.tagCont.showInitialOptions();

        self.tagCont.showDropOptionsTimeout(150);

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
        self.handles.annotatedWordHandle.text(self.currentChunk.text());

        self.tagCont.focus();

        self.state = this.states.INVALID;
        self.tagCont.currentTag = null;
        self.updateStatesView();
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

    TokenTagger.prototype.addOptionsAtBeginning = function(options){
        var self = this;
        self.tagCont.addOptions(options, true);
    };

    TokenTagger.prototype.addOptionsAtEnd = function(options){
        var self = this;
        self.tagCont.addOptions(options);
    };

    /**
     * Enum for visual state values.
     * @readonly
     * @enum {number}
     */
    TokenTagger.prototype.states = {
        INVALID: 0,
        OK: 1,
        ERROR: -1
    };

    TokenTagger.prototype.updateState = function(){
        if(!this.tagCont.currentTag)
            this.state = this.states.INVALID;
        else if(this.tagCont.currentTag.error)
            this.state = this.states.ERROR;
        else if (this.tagCont.currentTag.areAllValuesSet())
            this.state = this.states.OK;
        else
            this.state = this.states.INVALID;

        // this.updateStatesView();
    };

    /**
     * @param {TokenTagger.prototype.states | undefined} indicator - mode which should be shown, when undefined is given, takes the object state
     */
    TokenTagger.prototype.updateStatesView = function(indicator){
        var self = this;

        indicator = indicator || self.state;

        $.each(this.handles.stateIndicators, function(index, value){
            value.css('display', 'none');
        });

        // hide save button by default
        // this.handles.saveButton.css('display', 'none');
        this.handles.saveButton.addClass('disabled');

        switch(indicator){
            case self.states.OK:
                this.handles.stateIndicators['ok'].css('display', 'block');
                // this.handles.saveButton.css('display', 'block');
                this.handles.saveButton.removeClass('disabled');
                break;
            case self.states.INVALID:
                this.handles.stateIndicators['invalid'].css('display', 'block');
                break;
            case self.states.ERROR:
                this.handles.stateIndicators['error'].css('display', 'block');
                break;
            // todo - default settings?
        }
    };

    TokenTagger.prototype.hideList = function(){
        var self = this;
        self.tagCont.hideListTimeout();
    };

    TokenTagger.prototype.saveRequest = function () {
        var example = {
                "report_id":"100502",
                "from":169,
                "to":168,
                "text":"",
                "type":"nam_loc_astronomical",
                "annotation_type_id":"6",
                "stage":"final"
            };
        console.log(doAjax);
        console.log(getSelText);
        // console.log(doAjax("report_add_annotation", params, success, null, complete););
    };
    // tokenTagger = new TokenTagger($('#token-tagger-module'), $('#chunklist'));
    // tokenTagger.turnOnArrowKeys();

    var html = '<div id="token-tagger-module">' +
        '<h5>Anotating token: <i><span mod-id="anotated-word"></span></i></h5>' +
        '<p class="token-state-indicator" mod-id="state-ok"><span class="token-tagger-glyph token-tagger-glyph-green  glyphicon glyphicon-ok-sign" aria-hidden="true"></span> Possible tag</p>' +
        '<p class="token-state-indicator" mod-id="state-error"><span class="token-tagger-glyph token-tagger-glyph-red glyphicon glyphicon-remove-sign" aria-hidden="true"></span>  Invalid input tag</p>' +
        '<p class="token-state-indicator" mod-id="state-invalid"><span class="token-tagger-glyph token-tagger-glyph-yellow glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> Tag not precise enough</p>' +
        '<div class="form-group">'+
            '<button mod-id="button-save" class="btn btn-success token-tagger-btn disabled"><span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Save tag</button>'+
        '</div>'+
            '<div class="row">' +
                '<div class="col-xs-12">' +
                '<div class="btn-group">' +
                    '<button mod-id="button-prev" class="btn btn-default btn-xs token-tagger-btn"><span class="glyphicon glyphicon-circle-arrow-left" aria-hidden="true"></span> Previous token</button>' +
                    '<button mod-id="button-next" class="btn btn-default btn-xs token-tagger-btn">Next token <span class="glyphicon glyphicon-circle-arrow-right" aria-hidden="true"></span></button>' +
                '</div>' +
                '</div>' +
            '</div>' +
        '<div class="form-group">' +
            '<select mod-id="tag-select" class="form-control" onkeyup="tokenTagger.onInputTagKeyUp(this.value, event)"></select>' +
        '</div>' +
        '</div>';

    // changing element with new html
    $('#annotation-types > .tree > [groupid="22"]').html(html);
    tokenTagger = new TokenTagger($('#token-tagger-module'), $('chunklist'));
    tokenTagger.hideList();


    // console.log(a.style('opacity', 1));


    // $( document ).tooltip({
    //     position: {
    //         my: "center bottom-20",
    //         at: "center top",
    //         using: function( position, feedback ) {
    //             $( this ).css( position );
    //             $( "<div>" )
    //                 .addClass( "arrow" )
    //                 .addClass( feedback.vertical )
    //                 .addClass( feedback.horizontal )
    //                 .appendTo( this );
    //         }
    //     }
    // });
});

