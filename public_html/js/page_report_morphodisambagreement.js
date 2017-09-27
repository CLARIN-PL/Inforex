$(function () {

    /* utility functions */
    Array.prototype.insertNullsAtPosition = function (position, howManyNulls) {
        var nullArr = new Array(howManyNulls).fill(null);
        return this.slice(0,position).concat(nullArr).concat(this.slice(position));
    };

    Array.prototype.partition = function (f, firstCategory, secondCategory){
        var matched = [],
            unmatched = [],
            i = 0,
            j = this.length;

        for (; i < j; i++){
            (f.call(this, this[i], i) ? matched : unmatched).push(this[i]);
        }
        ret = {};
        ret[firstCategory] = unmatched;
        ret[secondCategory] = matched;
        return ret;
    };

    MorphoTaggerAgree = function MorphoTaggerAgree(handleModule, handleTokens, tokensTags, editableSelect, decisionA, decisionB){
        this.handles = {};
        this.handles.main = handleModule;
        this.handles.tokens = handleTokens;

        this.annotatorA = {};
        this.annotatorB = {};

        var annotatorsList = handleModule.find('.card-main .annotator');
        this.annotatorA.listHandle = annotatorsList[0];
        this.annotatorB.listHandle = annotatorsList[1];

        if(decisionA === null || decisionB === null){
            this.showAnnotatorsNotSetMsg();
        } else{
        this.init(decisionA, decisionB);
            // reference to MorphoTaggerAgree from MorphoTagger
            this.parent.that = this;
            this.parent.constructor(handleModule, handleTokens, tokensTags, editableSelect);
        }
    };
    MorphoTaggerAgree.prototype.parent = Object.create(MorphoTagger.prototype);


    /* Own functions */
    MorphoTaggerAgree.prototype.init = function(decisionA, decisionB){
        var self = this;
        self.attachAnnotatorDecisions(decisionA, decisionB);
    };

    MorphoTaggerAgree.prototype.showAnnotatorsNotSetMsg = function(){
        this.handles.main.find('[data-module-id="overlay"]').css({
            'display': 'block'
        });
    };

    MorphoTaggerAgree.prototype.attachAnnotatorDecisions = function(decisionA, decisionB){
        var self = this;
        var tokenHandle;

        for(var i = 0; i < self.handles.tokens.length; i++){
            tokenHandle = self.handles.tokens[i];

            var decisionsA = decisionA.filter(function(item){return 'an' + item.token_id === tokenHandle.id});
            var decisionsB = decisionB.filter(function(item){return 'an' + item.token_id === tokenHandle.id});

            $(tokenHandle).attr('data-agreement', JSON.stringify({
                a: decisionsA,
                b: decisionsB
            }));
        }
    };

    MorphoTaggerAgree.prototype.showAnnotatorsDecisions = function(tokenHandle, taggerTags){
        var self = this;
        var annotatorsDecision = JSON.parse(tokenHandle.getAttribute('data-agreement'));

        self.showSingleAnnotatorDecision(self.annotatorA.listHandle, taggerTags.slice(0), annotatorsDecision.a, 'A');
        self.showSingleAnnotatorDecision(self.annotatorB.listHandle, taggerTags.slice(0), annotatorsDecision.b, 'B');

        // self.updateFinalDecisionOptions();
    };

    MorphoTaggerAgree.prototype.updateFinalDecisionOptions = function(tokenCard){
        var self = this, copiedObject;

        var customTags = tokenCard.annotatorA.options.custom
            .concat(tokenCard.annotatorB.options.custom
                .filter(function(it){
                    return !it.isChoosenByBothAnnotators;
                }));
        console.log('append final decision', tokenCard);
        for(var i = 0 ; i < customTags.length; i++){
            copiedObject = Object.assign(customTags[i]);
            copiedObject.disamb='0';
            tokenCard.appendTagOption(copiedObject);
        }
    };

    MorphoTaggerAgree.prototype.showSingleAnnotatorDecision = function(listHandle, taggerTags, annotatorSelection, annotatorLetter){
        var self = this;
        var annotator = self.parent.mainTokenCard['annotator' + annotatorLetter];
        var cardStub = self.parent.mainTokenCard['annotator' + annotatorLetter].tokenCardStub;
        cardStub.list = $(listHandle).html('');

        if(annotatorLetter === 'B'){
            cardStub.listOptions= cardStub.listOptions
                .insertNullsAtPosition(
                    cardStub.listOptions.length
                    + self.parent.mainTokenCard.annotatorsMatchingCustomOptions.length
                    - annotator.options.custom.length,
                    self.parent.mainTokenCard.annotatorA.options.custom.length
                    - self.parent.mainTokenCard.annotatorsMatchingCustomOptions.length
                );

            for(var i = 0; i < annotator.options.custom.length - self.parent.mainTokenCard.annotatorsMatchingCustomOptions.length; i++){
                // console.log(self.parent.mainTokenCard.annotatorA.tokenCardStub);
                self.parent.mainTokenCard.annotatorA.tokenCardStub.appendTagOption(null);
            }
        }
        TokenCard.prototype.showListOptions.call(self.parent.mainTokenCard['annotator' + annotatorLetter].tokenCardStub);
    };

    MorphoTaggerAgree.prototype.markDoublySelectedOptions = function(tokenCard){
        var self = this;

        var setToGreen = tokenCard.annotatorsMatchingCustomOptions
            .concat(tokenCard.annotatorsMatchingOrdinaryOptions);

        var lis = tokenCard.list.find('li');

        var tag, toSet;
        lis = lis.filter(function(idx, li){
            tag  = JSON.parse(li.getAttribute('tag'));
            toSet = setToGreen.filter(function(it){
               return (tag.ctag_id === it.ctag_id
                    && tag.base_id === it.base_id);
            });

            if(toSet.length > 0){
                return true;
                // console.log(toSet);
                // console.log(li.addClass);
            }
            return false;
        });
        // console.log(lis);
        lis.map(function(idx, it){
            $(it).addClass('agreed');
        });
        // tokenCard.list.find('li').filter(function(idx, li){
        //     console.log(JSON.parse(li.getAttribute('tag')));
        //     return true;
        // });
        // for(var i = 0; i < self.)
    };

    /* Overriding parent functions */
    MorphoTaggerAgree.prototype.parent.saveDecision = function(){
      console.log('saving');
    };


    TokenCard.prototype.getAnnotatorDecision = function(annotatorLetter,annotatorSelection, taggerTags){
        var self = this;
        var annotator = self['annotator' + annotatorLetter];
        annotator.tokenCardStub = {
            disamb:{
                user: annotatorSelection
            },
            listOptions: [],
            appendTagOption: TokenCard.prototype.appendTagOption
        };

        // small cheat, running TokenCard function with tokenCardStub object as context this
        annotator.tokenCardStub.listOptions = TokenCard.prototype.getListTagOptions.call(annotator.tokenCardStub, taggerTags);
        annotator.options = annotator.tokenCardStub.listOptions;

        var customTags = annotator.tokenCardStub.listOptions.filter(function(it1){
            return self.listOptions.findIndex(function(it2){
                    return (it1.base_text + it1.ctag === it2.base_text + it1.ctag);
                }) < 0;
        });
        annotator.options = annotator.tokenCardStub.listOptions.partition(function(it1){
            return self.listOptions.findIndex(function(it2){
                    return (it1.base_text + it1.ctag === it2.base_text + it1.ctag);
            }) < 0;
        },'ordinary', 'custom');

        annotator.customTags = customTags;

        // place custom tags to the end of the list
        annotator.tokenCardStub.listOptions.map(function(it){
            it.isCustom = customTags.indexOf(it) > -1;
        });
        annotator.tokenCardStub.listOptions = annotator.tokenCardStub.listOptions.sort(function(it1, it2){
            if(it1.isCustom && it2.isCustom) return 0;
            if(it1.isCustom) return 1;
            if(it2.isCustom) return -1;

            return 0;
        });
    };

    TokenCard.prototype.getAnnotatorsDecisions = function(tokenHandle, taggerTags){
        var self = this;
        self.annotatorA = {};
        self.annotatorB = {};
        self.annotatorsMatchingCustomOptions = [];
        self.annotatorsMatchingOrdinaryOptions = [];

        var annotatorsDecision = JSON.parse(tokenHandle.getAttribute('data-agreement'));

        self.getAnnotatorDecision('A', annotatorsDecision.a, taggerTags);
        self.getAnnotatorDecision('B', annotatorsDecision.b, taggerTags);


        var markMatchingChoosenTags = function(it1, it2){
            if ((it1.ctag_id === it2.ctag_id)
                && it1.base_id === it2.base_id
                && it1.disamb === '1'
                && it2.disamb === '1'){
                it1.isChoosenByBothAnnotators = true;
                it2.isChoosenByBothAnnotators = true;
                return true;
            }
            return false;
        };
        var a,b,i,j;
        for(i = 0; i < self.annotatorA.options.custom.length; i++){
            for(j = 0; j < self.annotatorB.options.custom.length; j++ ){
                a = self.annotatorA.options.custom[i];
                b = self.annotatorB.options.custom[j];

                if(markMatchingChoosenTags(a,b)){
                    self.annotatorsMatchingCustomOptions.push(b);
                }
            }
        }
        for(i = 0; i < self.annotatorA.options.ordinary.length; i++){
            for(j = 0; j < self.annotatorB.options.ordinary.length; j++ ){
                a = self.annotatorA.options.ordinary[i];
                b = self.annotatorB.options.ordinary[j];

                if(markMatchingChoosenTags(a,b)){
                    self.annotatorsMatchingOrdinaryOptions.push(b);
                }
            }
        }

        var sortFcn = function(it1, it2){
            if(it1.isCustom && it2.isCustom){
                if(it1.isChoosenByBothAnnotators && it2.isChoosenByBothAnnotators)
                    return 0;
                if(it1.isChoosenByBothAnnotators)
                    return -1;
                if(it2.isChoosenByBothAnnotators)
                    return 1;
            }
            return 0;
        };
        // arrays are already sorted, only moving custom and unique list elements to the end
        self.annotatorA.tokenCardStub.listOptions.sort(sortFcn);
        self.annotatorB.tokenCardStub.listOptions.sort(sortFcn);
    };

    MorphoTaggerAgree.prototype.parent.updateTokenCards = function () {
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
            self.tokenCards[i].deselectAll();
            var currentTagVal;
            self.tokenCards[i].list.find('li').map(function(idx, li){
                currentTagVal = JSON.parse(li.getAttribute('tag'));
                currentTagVal.disamb = '0';
                li.setAttribute('tag', JSON.stringify(currentTagVal));
            });
            // initializing annotators decisions
            var tokenHandle = self.tokenCards[i].activeTokenHandle;
            if(tokenHandle){

                self.tokenCards[i].getAnnotatorsDecisions(tokenHandle, taggerTags);
                if(self.tokenCards[i].isMainCard){
                    self.that.showAnnotatorsDecisions(tokenHandle, taggerTags);
                }
                self.that.updateFinalDecisionOptions(self.tokenCards[i]);
                self.that.markDoublySelectedOptions(self.tokenCards[i]);

            }
        }
        self.currentTokenId = activeTokens[2].id.replace('an','');
    };

});