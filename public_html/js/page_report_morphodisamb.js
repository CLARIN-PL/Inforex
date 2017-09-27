/**
 * Created by wrauk on 02.08.17.
 */

$(function () {

    if (!Element.prototype.scrollIntoViewIfNeeded) {
        Element.prototype.scrollIntoViewIfNeeded = function (centerIfNeeded) {
            centerIfNeeded = arguments.length === 0 ? true : !!centerIfNeeded;

            var parent = this.parentNode,
                parentComputedStyle = window.getComputedStyle(parent, null),
                parentBorderTopWidth = parseInt(parentComputedStyle.getPropertyValue('border-top-width')),
                parentBorderLeftWidth = parseInt(parentComputedStyle.getPropertyValue('border-left-width')),
                overTop = this.offsetTop - parent.offsetTop < parent.scrollTop,
                overBottom = (this.offsetTop - parent.offsetTop + this.clientHeight - parentBorderTopWidth) > (parent.scrollTop + parent.clientHeight),
                overLeft = this.offsetLeft - parent.offsetLeft < parent.scrollLeft,
                overRight = (this.offsetLeft - parent.offsetLeft + this.clientWidth - parentBorderLeftWidth) > (parent.scrollLeft + parent.clientWidth),
                alignWithTop = overTop && !overBottom;

            if ((overTop || overBottom) && centerIfNeeded) {
                parent.scrollTop = this.offsetTop - parent.offsetTop - parent.clientHeight / 2 - parentBorderTopWidth + this.clientHeight / 2;
            }

            if ((overLeft || overRight) && centerIfNeeded) {
                parent.scrollLeft = this.offsetLeft - parent.offsetLeft - parent.clientWidth / 2 - parentBorderLeftWidth + this.clientWidth / 2;
            }

            if ((overTop || overBottom || overLeft || overRight) && !centerIfNeeded) {
                this.scrollIntoView(alignWithTop);
            }
        };
    }

    /**
     * initialized Tooltip object
     * @param {jQuery} appendToHandle
     * @constructor
     */
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
            .addClass("visible");
        return self;
    };

    Tooltip.prototype.addLine = function(line){
        this.html += '<br>\n' + line;
        this.show();
        return this;
    };

    Tooltip.prototype.hide = function(){
        this.handle.removeClass('visible');
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

        this.chosenValues = [];

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
        this.initOnInputChange();
    }

    TagContainer.prototype.init = function(){
        var self = this;

        // self.tooltip = new Tooltip(self.moduleHandle);
        self.tooltip = new Tooltip($('#editable-select-container'));

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

    TagContainer.prototype.get = function(){
        return this.inputVal;
    };

    TagContainer.prototype.initOnInputChange = function () {
        var self = this;
        self.editableSelectHandle.on('keyup', function(e){
            self.onInputTagChange(e, self.editableSelectHandle[0].value);
        });
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
            self.editableSelectHandle.editableSelect('clear');
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
        this.clear();
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
            } else{
                self.tooltip.hide();
            }
        });
    };

    TagContainer.prototype.onInputTagChange = function(event, inputVal) {
        var self = this;
        self.inputVal = inputVal;

        if(event.key === 'Backspace' && inputVal === ''){
            self.showInitialOptions();
            self.showDropOptionsTimeout()
        } else if(this.currentTag) {
                var explodedTags = inputVal.split(":");
                if(explodedTags.length > 1){
                    explodedTags = explodedTags.filter(function(t){return t !== '';});
                    self.currentTag.assignTags(explodedTags.splice(1));
                    if(event.key === ':')
                        self.showNextPossibleTags();
                }
        } else{
            self.currentTag = self.getCategoryByAbbr(inputVal.replace(':',''));
            if(self.currentTag) self.showNextPossibleTags();
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

    TagContainer.prototype.getTagDb = function(){
        var self = this;
       // todo check if ready
        var foundTag = self.possibleTags.find(function(tag) {
            return tag.value === self.currentTag.abbr + '_' + self.currentTag.chosenValues.join('_');
        });
        return foundTag;
    };

    /*
     * generating DB records
     *
     */

    /*
    function getAllWithAll(name, arr){
        var attr = arr.map(function(x){
            return Tag.prototype.data.attributes[x];
        });
        attr = [[name]].concat(attr);

        var allWithAll = cartesianProduct(attr);
        // console.log(allWithAll);
        return allWithAll;
    }

    function cartesianProduct(arr)
    {
        return arr.reduce(function(a,b){
            return a.map(function(x){
                return b.map(function(y){
                    return x.concat(y);
                })
            }).reduce(function(a,b){ return a.concat(b) },[])
        }, [[]])
    }

    function getSqlQuery(arr){
        var sql_part = '';
        for(var i = 0; i < arr.length; i++){
            sql_part += " ('NAME', '',  '22',  '66',  '0',  'SHOW_NAME', 'background: #7EE7AC; border: 1px solid #2ecc71', '0', '1' ),\n".replace('NAME', arr[i][1]).replace('SHOW_NAME', arr[i][0]);
        }
        return sql_part;
    }

    TokenTaggerOld.prototype.listAllPossibleTags = function(){
        var classes =  this.tagCont.data.classes;
        var categories = Tag.prototype.data.attributes;
        console.log(classes);
        console.log(categories);

        var i, j, k, currentCatAttr, allPosibilities = [];
        for(i = 0; i < classes.length; i++){
            // if (classes[i].abbr === 'ppas')
            allPosibilities = allPosibilities.concat(getAllWithAll(classes[i].abbr, classes[i].categories));
        }

        var allPosibilitiesStr = allPosibilities.map(function (x) {
            return [x.join(':'), x.join('_')];
        });
        var query = 'INSERT INTO  `inforex`.`annotation_types` (`name` , `description` , `group_id` , `annotation_subset_id` , `level` , `short_description` , `css` , `cross_sentence` , `shortlist`) ' +
            'VALUES\n ';
        console.log(query + getSqlQuery(allPosibilitiesStr).replace(/.$/,";") );
    };

    // TokenTaggerOld.listAllPossibleTags();

    */

    function TokenSelect(parent, moduleHandle, selectHandle, baseHandle, btnSaveHandle){
        this.tagCont = new TagContainer(selectHandle, moduleHandle);
        this.parent = parent;
        this.handles = {
            select: selectHandle,
            base: baseHandle,
            save: btnSaveHandle
        };

        this.state = {
            baseReady: false,
            tagReady: false
        };

        this.init();
        this.disableEnableButton();
    }

    TokenSelect.prototype.disableEnableButton = function(enable){
        enable = enable || false;
        this.handles.save.attr("disabled", !enable);
    };

    TokenSelect.prototype.updateButtonState = function(){
        this.disableEnableButton(this.state.tagReady && this.state.baseReady);
    };

    TokenSelect.prototype.updateTagState = function(){
        var self = this;

        // Invalid state
        if(!this.tagCont.currentTag || this.tagCont.currentTag.error)
            this.state.tagReady = false;

        else
            this.state.tagReady = this.tagCont.currentTag.areAllValuesSet()
    };

    TokenSelect.prototype.init = function(){
        var self = this;

        $(self.handles.base).on('keyup',function(){
            self.state.baseReady =  this.value.length > 0;
            self.updateButtonState();
        });

        $(self.tagCont.editableSelectHandle).on('keyup select', function(e){
            self.updateTagState();
            self.updateButtonState();
        });

        self.handles.save.click(function(e){
           self.addToken();
        });
    };

    TokenSelect.prototype.addToken = function(){
        var self = this;

        self.parent.addTagOption({
            token_id: self.parent.currentTokenId,
            base_text: self.handles.base.val(),
            ctag: self.tagCont.get(),
            custom: true
        });
        var items = self.parent.mainTokenCard.list.children();
        self.parent.mainTokenCard.toggleSelectListItem(items[items.length -1], false);

        self.clearInputs();
    };

    TokenSelect.prototype.clearInputs = function(){
        var self = this;
        self.state = {
            baseReady: false,
            tagReady: false
        };
        self.handles.base.val('');
        self.tagCont.showInitialOptions();
        self.tagCont.hideListTimeout();
        self.updateButtonState();
    };

    TokenCard = function TokenCard( handle, list, tokenHandle,  index, isMainCard){
        this.handle= handle;
        this.list= list;
        this.tokenHandle = tokenHandle;
        this.index = index;

        this.decisions = null;
        this.isMainCard = isMainCard;
        if(isMainCard)
            this.initMain();
    };

    TokenCard.prototype.initMain = function(){
        var self = this;

        self.list.on('click', 'li', function(e){
            self.toggleSelectListItem(e.currentTarget, e.ctrlKey);
        });

        self.list.on('mouseenter','li', function(e){
            self.focusOnListItem(e.currentTarget);
        });
    };

    TokenCard.prototype.getListTagOptions = function(taggerTags){
        var self = this, item, itemInner, j, userDecision;
        // shallow copy taggerTags
        taggerTags = taggerTags.slice();

        // removing disambs contained both in tool and user
        for(var i = 0; i < self.disamb.user.length; i++){
            userDecision = self.disamb.user[i];
            var toDeleteIdx = taggerTags.findIndex(function(item){
                return userDecision.ctag === item.ctag && userDecision.base_text === item.base_text;
            });
            if(toDeleteIdx > -1){
                taggerTags.splice(toDeleteIdx, 1);
            }
        }
        // adding user disabms
        return (taggerTags.concat(self.disamb.user))
            .sort(function(it1, it2){
                return (it1.base_text + it1.ctag).localeCompare(it2.base_text + it2.ctag);
            });
    };

    TokenCard.prototype.showListOptions = function () {
        var self = this;

        for(var i = 0; i < self.listOptions.length; i++){
            self.appendTagOption(self.listOptions[i]);
        }
    };

    TokenCard.prototype.update = function(settings){
        var self = this;
        self.activeTokenHandle = settings.token;

        self.list.html('');

        settings.loading ? self.handle.addClass('card-loading') : self.handle.removeClass('card-loading');

        if(settings.inactive){
            self.tokenHandle.text('∅');
            self.handle.addClass('inactive');
            return;
        }
        self.handle.removeClass('inactive');
        self.tokenHandle.text(settings.token.innerText);

        self.disamb = JSON.parse(settings.token.getAttribute('disamb'));
        self.listOptions = self.getListTagOptions(settings.taggerTags);


        self.showListOptions();
    };

    TokenCard.prototype.toggleSelect = function(li, ctrlKey){
        var self = this;
        li = $(li);

        // not allowing for 'ign' disamb selection
        if(li.find('span.tag').text() === 'ign')
            return;


        if(li.hasClass('selected')){
            li.removeClass('selected');
            if(!ctrlKey)
                self.deselectAll();
            return;
        }
        if(!ctrlKey)
            self.deselectAll();
        li.addClass('selected');
    };

    TokenCard.prototype.deselectAll = function (li) {
        var self = this;
        self.list.find('li').removeClass('selected');
    };

    TokenCard.prototype.toggleSelectFocusedListItem = function (ctrlKey) {
        var self = this;
        self.toggleSelect(self.list.find('li.focused'), ctrlKey);
    };

    TokenCard.prototype.toggleSelectListItem = function (selectedElement, ctrlKey) {
        var self = this;
        self.toggleSelect(selectedElement, ctrlKey);
    };

    TokenCard.prototype.isDecisionDifferent = function(){
        var self = this;
        return JSON.stringify(self.decisions) !== JSON.stringify(self.getSelectedOptions());
    };

    TokenCard.prototype.getSelectedOptions = function () {
        var self = this;
        var selected = self.list.find('.selected');

        if (selected.length === 0){
            return null;
        }
        return selected.toArray().map(function(elem, index){
            return JSON.parse(elem.getAttribute('tag'));
        });
    };

    TokenCard.prototype.getDisambDifference = function(selected, tool){
        var diff = [], searchedIdx, toolItem, selItem;
        for(var i = 0; i < tool.length; i++){
            toolItem = tool[i];
            searchedIdx = selected.findIndex(function(item){
                return toolItem.ctag === item.ctag && toolItem.base_text === item.base_text;
            });

            // if selected is contained within tool
            if(searchedIdx > -1){
                // if tool item not originally disamb
                if(toolItem.disamb === '0'){
                    diff.push(selected[searchedIdx]);
                }
            }

            // if didn't found selected
            else {
                diff.push({
                    base_text: toolItem.base_text,
                    ctag: toolItem.ctag,
                    disamb: "0",
                    token_id: toolItem.token_id
                })
            }
        }

        // return diff;

        for(i=0; i < selected.length; i++){
            selItem = selected[i];
            searchedIdx = tool.findIndex(function(item){
                return selItem.ctag === item.ctag && selItem.base_text === item.base_text;
            });

            // if selected is contained within tool
            if(searchedIdx > -1){
                // if tool item not originally disamb
                if(tool.disamb === '0'){
                    diff.push(selItem);
                }
            }

            // if didn't found selected
            else {
                diff.push({
                    base_text: selItem.base_text,
                    ctag: selItem.ctag,
                    disamb: "1",
                    token_id: selItem.token_id
                })
            }

        }

        return diff;
    };

    TokenCard.prototype.getDecision = function () {
        var self = this;
        var selected  = self.getSelectedOptions() || [];


        var newUserDecision = self.getDisambDifference(selected, self.disamb.tool);
        return newUserDecision;
    };

    TokenCard.prototype.appendTagOption = function(tagObject){
        if (tagObject === null){
            this.list.append('<li></li>');
            return;
        }
        var classed = tagObject.disamb === '1' ? 'selected' : '';

        this.list.append("<li " + 'class= "'  + classed + '"'
            +"tag= '"+ JSON.stringify(tagObject) +"'>"
            +'<span class="tag-base"><span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span>' + tagObject.base_text +'</span> &nbsp;'
            +'<span class="tag">' + tagObject.ctag +'</span>'
            +'</li>');
    };

    TokenCard.prototype.hasDecisionChanged = function(newDecision){
        var self = this;


        if(self.disamb.user.length !== newDecision.length)
            return true;

        var item1, item2;
        for(var i = 0; i < self.disamb.user.length; i++){
            item1 = self.disamb.user[i];
            item2 = newDecision.find(function(it){
                return it.ctag === item1.ctag
                    && it.base_text === item1.base_text
                    && it.disamb === item1.disamb;
            });

            if(!item2)
                return true;
        }

        return false;
    };

    TokenCard.prototype.saveUserDecisionToAttribute = function(decision){
        var self = this;
        $(self.activeTokenHandle).attr('disamb', JSON.stringify({
            tool: self.disamb.tool,
            user: decision
        }));
    };

    TokenCard.prototype.focusOfFirstListItem = function () {
        var children = this.list.children();
        $(children[0]).addClass('focused');
    };

    TokenCard.prototype.focusOnListItem = function (item) {
        this.list.children().removeClass('focused');
        $(item).addClass('focused');
    };

    TokenCard.prototype.scrollToElement = function(element){
        element[0].scrollIntoViewIfNeeded(false);
    };

    TokenCard.prototype.moveFocusUp = function () {
        var listEls = this.list.children();

        var li, scrollTo;
        for(var i = 0; i < listEls.length; i++){
            li = $(listEls[i]);
            if(li.hasClass('focused')){
                if(i -1 >= 0 ){
                    li.removeClass('focused');
                    this.scrollToElement($(listEls[i-1]).addClass('focused'));
                }
                return;
            }
        }

    };

    TokenCard.prototype.moveFocusDown = function () {
        var listEls = this.list.children();

        var li, scrollTo;
        for(var i = 0; i < listEls.length; i++){
            li = $(listEls[i]);
            if(li.hasClass('focused')){
                if(i + 1 < listEls.length){
                    li.removeClass('focused');
                    this.scrollToElement($(listEls[i+1]).addClass('focused'));
                }
                return;
            }
        }
    };

    /**
     * Innitialized MorphoTagger module object
     * @param {jQuery} handleModule
     * @param {jQuery[]} handleTokens
     * @param {Object[]} tokensTags
     * @param {jQuery} editableSelect
     * @constructor
     */
    MorphoTagger = function MorphoTagger(handleModule, handleTokens, tokensTags, editableSelect){
        this.handles = {
            main: handleModule,
            tokens: handleTokens
        };
        this.activeTokenOffset = 0;
        this.tokensTags = tokensTags;
        this.loadingCards = new Array(5).fill(false);

        this.init();
        this.state = {};
        this.state.inputChanged = false;

        this.tokenSelect = new TokenSelect(this, handleModule, editableSelect, this.handles.main.find('#lemma-base') ,this.handles.main.find('#add-tag'));
    };

    MorphoTagger.prototype.initUserDecisions = function(){
        var self = this, id, disambTool, disambUser, j, tag;
        for(var i = 0; i < self.handles.tokens.length; i++){
            id = self.handles.tokens[i].id.replace('an', '');
            disambTool = [];
            disambUser = [];

            for(j = 0; j < self.tokensTags.length; j++){
                tag = self.tokensTags[j];
                if(tag.token_id === id){
                    if(tag.user_id){
                        disambUser.push(tag);
                    }
                    else if(tag.disamb === '1'){
                        disambTool.push(tag);
                    }
                }
            }

            $(self.handles.tokens[i]).attr('disamb', JSON.stringify({
                tool: disambTool,
                user: disambUser
            }));
        }
    };

    MorphoTagger.prototype.addTagOption = function(tagObject){
        var self = this;

        self.tokensTags.push(tagObject);
        if(tagObject.base_text !== '' && tagObject.ctag!== ''){
            self.mainTokenCard.appendTagOption(tagObject);
        }
    };

    MorphoTagger.prototype.init = function(){
        var self = this;
        self.initUserDecisions();

        var tokenCardsHandle = self.handles.main.find('.token-card-content');
        var mainCardIdx = Math.floor(tokenCardsHandle.length/ 2);

        self.tokenCards = tokenCardsHandle.map(function(index,card){
            card = $(card);
            return new TokenCard(card, card.find('ul.possible-tags-list').not('.annotator'), card.find('.morpho-token'), index, index === mainCardIdx);
        });

        self.mainTokenCard = self.tokenCards[mainCardIdx];
        self.initButtons();
        self.initTokenClicks();
        self.initKeyboardShortcuts();
        self.updateTokens();

        self.mainTokenCard.focusOfFirstListItem();
    };

    MorphoTagger.prototype.initTokenClicks = function(){
      var self = this;

      self.handles.tokens.click(function(e){
          var innerSelf = this;
          var offset = self.handles.tokens.toArray().findIndex(function(tok){
              return tok.id === innerSelf.id;
          });
          self.moveTokenToOffset(offset);
      });
    };

    /**
     * Saves the decision both in front and backend
     * @returns {boolean} indicates if there was anything to save
     */
    MorphoTagger.prototype.saveDecision = function () {
        var self = this;
        var decision = self.mainTokenCard.getDecision();

        if(!self.mainTokenCard.hasDecisionChanged(decision))
            return false;

        self.mainTokenCard.saveUserDecisionToAttribute(decision);

        var savingDecisionTokenId = self.currentTokenId;

        var success = function(data){
            var idx = self.loadingCards.indexOf(savingDecisionTokenId);
            self.loadingCards[idx] = false;
            self.tokenCards[idx].handle.removeClass('card-loading');
            console.log(data);
        };

        var error = function(error_code){
            console.log(error_code);
        };
        var complete = function(){
            console.log('complete');
        };

        doAjax('tokens_tags_add', {token_id: savingDecisionTokenId, tags:decision}, success, error, complete);
        return true;
    };

    MorphoTagger.prototype.initButtons = function () {
        var self = this;
        self.handles.prevBtn = self.handles.main.find("button#prev");
        self.handles.nextBtn = self.handles.main.find("button#next");

        self.handles.prevBtn.on('click', function(){
           self.moveToPrevToken();
        });

        self.handles.nextBtn.on('click', function(){
            self.moveToNextToken();
        });
    };

    MorphoTagger.prototype.initKeyboardShortcuts = function(){
        var self = this;
        $(document).on('keydown', function(e){

            // if space is pressed
            if(e.key === ' '){
                e.preventDefault();
                // saving focused item
                self.mainTokenCard.toggleSelectFocusedListItem(e.ctrlKey);
            }
            else if(e.key === 'ArrowLeft'){
                e.preventDefault();
                self.moveToPrevToken();
            }else if(e.key === 'ArrowRight'){
                e.preventDefault();
                self.moveToNextToken();
            }else if(e.key === 'ArrowUp'){
                e.preventDefault();
                self.mainTokenCard.moveFocusUp();
            }else if(e.key === 'ArrowDown'){
                e.preventDefault();
                self.mainTokenCard.moveFocusDown();
            }
        });
    };

    MorphoTagger.prototype.updateTokens = function () {
        var self = this;
        self.handles.tokens.removeClass('token-tagger-active');
        $(self.handles.tokens[self.activeTokenOffset]).addClass('token-tagger-active');

        self.updateTokenCards();
    };

    MorphoTagger.prototype.removeDuplicatesPossibilities = function(arr){
        for(var i= arr.length -1; i >= 0; i--){
            // user_id present could mean there are duplicate entries
            if(arr[i].user_id){
                var itemIdx = arr.findIndex(function(item){
                    return item.ctag_id === arr[i].ctag_id && item.base_id === arr[i].base_id && item.user_id === null;
                });
                if(itemIdx > -1)
                    arr.splice(itemIdx,1);
            }
        }
        return arr;
    };

    MorphoTagger.prototype.updateTokenCards = function () {
        var self = this, i, j, taggerTags;
        var activeTokens = new Array(self.handles.tokens.length).fill(null);
        var tokensLen = self.handles.tokens.length;

        var currentTokenIdx = self.activeTokenOffset - Math.ceil(self.tokenCards.length / 2);

        for (i = 0; i < activeTokens.length; i++){
            currentTokenIdx++;

            if(currentTokenIdx < 0 || currentTokenIdx >= tokensLen) continue;
            activeTokens[i] = self.handles.tokens[currentTokenIdx];
        }

        for(i=0; i< self.tokenCards.length; i++){
            taggerTags = [];
            if (activeTokens[i]) {
                taggerTags = self.tokensTags.filter(function (x) {
                    return x.token_id === activeTokens[i].id.replace('an', '') && !x.user_id;
                });
            }
            self.tokenCards[i].update({
                loading: !!self.loadingCards[i],
                inactive: !activeTokens[i],
                token: activeTokens[i],
                taggerTags: taggerTags
            });
        }
        self.currentTokenId = activeTokens[2].id.replace('an','');

    };

    MorphoTagger.prototype.afterMoveToken = function(){
        var self = this;
        self.updateTokens();
        self.mainTokenCard.focusOfFirstListItem();
        self.tokenSelect.clearInputs();

        // scroll to active token - todo check compatibility
        self.handles.tokens[self.activeTokenOffset].scrollIntoViewIfNeeded(true);
    };

    MorphoTagger.prototype.moveTokenToOffset = function(offset){
        var self = this;

        // don't allow going if any card is loading
        if(self.loadingCards.some(function(card){
            return !!card;
        }))
            return;

        if(self.saveDecision())
            self.loadingCards[self.mainTokenCard.index] = self.currentTokenId;


        self.activeTokenOffset = offset;
        self.loadingCards.push(false);
        self.afterMoveToken();
    };

    MorphoTagger.prototype.moveToNextToken = function(){
        var self = this;

        // don't allow going to the next if not finished saving
        if(self.loadingCards[0])
            return;


        if(self.saveDecision())
            self.loadingCards[self.mainTokenCard.index] = self.currentTokenId;

        if(self.activeTokenOffset +1 < self.handles.tokens.length ){
            self.activeTokenOffset++;

            self.loadingCards.shift();
            self.loadingCards.push(false);
            self.afterMoveToken();
        }
    };

    MorphoTagger.prototype.moveToPrevToken = function(){
        var self = this;

        // don't allow going to the prev token if not finished saving
        if(self.loadingCards[self.loadingCards.length -1])
            return;

        if(self.saveDecision())
            self.loadingCards[self.mainTokenCard.index] = self.currentTokenId;

        if(self.activeTokenOffset > 0){
            self.activeTokenOffset--;

            self.loadingCards.pop();
            self.loadingCards.unshift(false);
            self.afterMoveToken();
        }
    };
});

