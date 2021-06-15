$(function () {

    /* utility functions */
    Array.prototype.partition = function (f, firstCategory, secondCategory){
        var matched = [],
            unmatched = [],
            i = 0,
            j = this.length;

        for (; i < j; i++){
            (f.call(this, this[i], i) ? matched : unmatched).push(this[i]);
        }
        var ret = {};
        ret[firstCategory] = unmatched;
        ret[secondCategory] = matched;
        return ret;
    };

    MorphoTaggerAgree = function MorphoTaggerAgree(handleModule, handleTokens, tokensTags, editableSelect, finalDecision, decisionA, decisionB){
        this.handles = {};
        this.handles.main = handleModule;
        this.handles.tokens = handleTokens;

        this.taggerTags = tokensTags;
        var taggerTagsCopy = JSON.parse(JSON.stringify(tokensTags)).map(function(tag){
            tag.disamb = '0';
            return tag;
        });

        this.finalTags = taggerTagsCopy.concat(finalDecision);

        this.annotatorA = {};
        this.annotatorB = {};

        var annotatorsList = handleModule.find('.card-main .annotator');
        this.annotatorA.listHandle = annotatorsList[0];
        this.annotatorB.listHandle = annotatorsList[1];

        // reference to MorphoTaggerAgree from MorphoTagger

        if(decisionA === null || decisionB === null){
            this.showAnnotatorsNotSetMsg();
        } else{
            this.init(decisionA, decisionB);
            // reference to MorphoTaggerAgree from MorphoTagger
            this.parent.that = this;
            this.parent.constructor(handleModule, handleTokens, this.finalTags, editableSelect);
        }
    };
    MorphoTaggerAgree.prototype.parent = Object.create(MorphoTagger.prototype);


    /* Own functions */
    MorphoTaggerAgree.prototype.init = function(decisionA, decisionB){
        var self = this;
        self.attachAnnotatorDecisions(decisionA, decisionB);
    };

    MorphoTaggerAgree.prototype.showAnnotatorsNotSetMsg = function(){
        this.parent.handles = {};
        this.parent.handles.main = this.handles.main;
        this.parent.showErrorMsg('Please choose annotators to compare. <br> (Press <i class="fa fa-cog fa-4" aria-hidden="true"></i> icon in top right corner)');
    };

    MorphoTaggerAgree.prototype.attachAnnotatorDecisions = function(decisionA, decisionB){
        var self = this;
        var tokenHandle;

        for(var i = 0; i < self.handles.tokens.length; i++){
            tokenHandle = self.handles.tokens[i];

            var decisionsA = decisionA.filter(function(item){return 'an' + item.token_id === tokenHandle.id});
            var decisionsB = decisionB.filter(function(item){return 'an' + item.token_id === tokenHandle.id});

            $(tokenHandle).data('data-agreement', {
                a: decisionsA,
                b: decisionsB
            });
        }
    };

    MorphoTaggerAgree.prototype.showAnnotatorsDecisions = function(tokenHandle, taggerTags){
        var self = this;
        var annotatorsDecision = $(tokenHandle).data('data-agreement');

        self.prepSingleAnnotatorDecision(self.annotatorA.listHandle, taggerTags.slice(0), annotatorsDecision.a, 'A');
        self.prepSingleAnnotatorDecision(self.annotatorB.listHandle, taggerTags.slice(0), annotatorsDecision.b, 'B');

        // self.updateFinalDecisionOptions();
    };

    MorphoTaggerAgree.prototype.updateFinalDecisionOptions = function(tokenCard){
        var self = this, copiedObject, i;

        var customTags = tokenCard.annotatorA.options.custom
            .concat(tokenCard.annotatorB.options.custom
                .filter(function(it){
                    return !it.isChoosenByBothAnnotators;
                }));

        for(i = 0 ; i < customTags.length; i++){
            // copy object
            copiedObject = JSON.parse(JSON.stringify(customTags[i]));
            copiedObject.disamb='0';
            tokenCard.listOptions.push(copiedObject);
        }
        tokenCard.list.html('');
        tokenCard.sortListOptions();
        tokenCard.showListOptions();

        if(tokenCard.isMainCard){
            function tokensTheSame(it1, it2){
                return (it1.base_text + it1.ctag).localeCompare(it2.base_text + it2.ctag) === 0;
            }
            // placing empty elements into annotators list
            var listA = self.parent.mainTokenCard.annotatorA.tokenCardStub.listOptions;
            var listB = self.parent.mainTokenCard.annotatorB.tokenCardStub.listOptions;
            for(i = 0 ; i < tokenCard.listOptions.length; i++){
                if(!listA[i] || !tokensTheSame(listA[i], tokenCard.listOptions[i]))
                    listA.splice(i, 0, null);
                if(!listB[i] || !tokensTheSame(listB[i], tokenCard.listOptions[i]))
                    listB.splice(i, 0, null);
            }
            self.parent.mainTokenCard.annotatorA.tokenCardStub.list.html('');
            self.parent.mainTokenCard.annotatorB.tokenCardStub.list.html('');
            TokenCard.prototype.showListOptions.call(self.parent.mainTokenCard.annotatorA.tokenCardStub);
            TokenCard.prototype.showListOptions.call(self.parent.mainTokenCard.annotatorB.tokenCardStub);
        }
    };

    MorphoTaggerAgree.prototype.prepSingleAnnotatorDecision = function(listHandle, taggerTags, annotatorSelection, annotatorLetter){
        var self = this;
        var annotator = self.parent.mainTokenCard['annotator' + annotatorLetter];
        var cardStub = self.parent.mainTokenCard['annotator' + annotatorLetter].tokenCardStub;
        cardStub.list = $(listHandle);
    };

    MorphoTaggerAgree.prototype.markDoublySelectedOptions = function(tokenCard){
        var self = this;

        var setToGreen = tokenCard.annotatorsMatchingCustomOptions
            .concat(tokenCard.annotatorsMatchingOrdinaryOptions);

        var lis = tokenCard.list.find('li');

        var tag, toSet;
        lis = lis.filter(function(idx, li){
            tag  = $(li).data('tag');
            toSet = setToGreen.filter(function(it){
               return (tag.ctag_id === it.ctag_id
                    && tag.base_id === it.base_id);
            });

            return (toSet.length > 0);
        });

        lis.map(function(idx, it){
            $(it).addClass('agreed selected');
        });
    };

    /* Overriding parent functions */
    MorphoTaggerAgree.prototype.parent.saveDecision = function(){
        var self = this;

        var decision = self.mainTokenCard.getFinalDecision();
        if(!self.mainTokenCard.hasDecisionChanged(decision))
            return false;
        self.mainTokenCard.saveUserDecisionToAttribute(decision);

        var savingDecisionTokenId = self.currentTokenId;

        var success = function(data){
            var idx = self.loadingCards.indexOf(savingDecisionTokenId);
            self.loadingCards[idx] = false;
            self.tokenCards[idx].handle.removeClass('card-loading');
        };

        var error = function(error_code){
            // console.log(error_code);
        };
        var complete = function(){
            // console.log('complete');
        };

        doAjax('tokens_tags_final_add', {token_id: savingDecisionTokenId, tags:decision, tagset: self.tagset}, success, error, complete);
        return true;
    };

    TokenCard.prototype.updateSelectedListElementsTags = function () {
        var self = this;
        var selected = self.list.find('.selected');

        var tag;
        selected.map(function(idx, it){
            tag = $(it).data('tag');
            tag.user_id = '0'; // random user id (just need to be set)
            tag.disamb = '1';
            $(it).data('tag', tag);
        });
    };

    TokenCard.prototype.getFinalDecision = function(){
        this.updateSelectedListElementsTags();
        return this.getSelectedOptions() || [];
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
                    return (it1.base_text + it1.ctag).localeCompare(it2.base_text + it2.ctag) === 0;
                }) < 0;
        });
        annotator.options = annotator.tokenCardStub.listOptions.partition(function(it1){
            return self.listOptions.findIndex(function(it2){
                    return (it1.base_text + it1.ctag).localeCompare(it2.base_text + it2.ctag) === 0;
            }) < 0;
        },'ordinary', 'custom');

        annotator.customTags = customTags;

        // place custom tags to the end of the list
        annotator.tokenCardStub.listOptions.map(function(it){
            it.isCustom = customTags.indexOf(it) > -1;
        });

        annotator.tokenCardStub.listOptions = annotator.tokenCardStub.listOptions.sort(function(it1, it2){
            return (it1.base_text + it1.ctag).localeCompare(it2.base_text + it2.ctag);
        });
    };

    TokenCard.prototype.getAnnotatorsDecisions = function(tokenHandle, taggerTags){
        var self = this;
        self.annotatorA = {};
        self.annotatorB = {};
        self.annotatorsMatchingCustomOptions = [];
        self.annotatorsMatchingOrdinaryOptions = [];

        var annotatorsDecision = $(tokenHandle).data('data-agreement');

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
    };

    MorphoTaggerAgree.prototype.parent.updateTokenCards = function () {
        var self = this, i, j, taggerTags;

        var activeTokens = new Array(Math.max(self.handles.tokens.length,3)).fill(null);
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

            var currentTagVal;
            self.tokenCards[i].list.find('li').map(function(idx, li){
                currentTagVal = $(li).data('tag');

                if(!currentTagVal.user_id){
                    currentTagVal.disamb = '0';
                    $(li).data('tag', currentTagVal);
                    $(li).removeClass('selected');
                }
            });
            // initializing annotators decisions
            var tokenHandle = self.tokenCards[i].activeTokenHandle;
            if(tokenHandle){

                var originalTaggerTags = self.that.taggerTags.filter(function (x) {
                    return x.token_id === activeTokens[i].id.replace('an', '') && !x.user_id;
                });

                self.tokenCards[i].getAnnotatorsDecisions(tokenHandle, originalTaggerTags);
                // self.tokenCards[i].getAnnotatorsDecisions(tokenHandle, self.that.taggerTags);
                if(self.tokenCards[i].isMainCard){
                    // passing 'clean' taggerTags without final decision
                    self.that.showAnnotatorsDecisions(tokenHandle, self.that.taggerTags);
                }
                self.that.updateFinalDecisionOptions(self.tokenCards[i]);

                // todo alert user if he want's to exit page witout saving!!
                if(self.tokenCards[i].disamb.user.length === 0)
                    self.that.markDoublySelectedOptions(self.tokenCards[i]);

            }
        }
        self.currentTokenId = activeTokens[1].id.replace('an','');
    };
});