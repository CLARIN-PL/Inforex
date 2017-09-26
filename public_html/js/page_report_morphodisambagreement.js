$(function () {

    /* utility functions */
    Array.prototype.insertNullsAtPosition = function (position, howManyNulls) {
        var nullArr = new Array(howManyNulls).fill(null);
        return this.slice(0,position).concat(nullArr).concat(this.slice(position));
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

        this.init(decisionA, decisionB);

        // reference to MorphoTaggerAgree from MorphoTagger
        this.parent.that = this;
        this.parent.constructor(handleModule, handleTokens, tokensTags, editableSelect);
    };
    MorphoTaggerAgree.prototype.parent = Object.create(MorphoTagger.prototype);


    /* Own functions */
    MorphoTaggerAgree.prototype.init = function(decisionA, decisionB){
        var self = this;
        self.attachAnnotatorDecisions(decisionA, decisionB);
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

        self.updateFinalDecisionOptions();
    };

    MorphoTaggerAgree.prototype.updateFinalDecisionOptions = function(){
        var self = this;
        var customTags = self.annotatorA.customTags.concat(self.annotatorB.customTags);
        for(var i = 0 ; i < customTags.length; i++){
            customTags[i].disamb=0;
            self.parent.mainTokenCard.appendTagOption(customTags[i]);
        }

    };

    MorphoTaggerAgree.prototype.showSingleAnnotatorDecision = function(listHandle, taggerTags, annotatorSelection, annotatorLetter){
        var self = this;
        var annotator = self['annotator' + annotatorLetter];
        $(listHandle).html('');
        annotator.tokenCardStub = {
            disamb:{
                user: annotatorSelection
            },
            listOptions: [],
            list: $(listHandle),
            appendTagOption: TokenCard.prototype.appendTagOption
        };

        // small cheat, running TokenCard function with tokenCardStub object as context this
        annotator.tokenCardStub.listOptions = TokenCard.prototype.getListTagOptions.call(annotator.tokenCardStub, taggerTags);
        annotator.options = annotator.tokenCardStub.listOptions;
        var customTags = annotator.tokenCardStub.listOptions.filter(function(it1){
            return self.parent.mainTokenCard.listOptions.findIndex(function(it2){
                return (it1.base_text + it1.ctag === it2.base_text + it1.ctag);
            }) < 0;
        });

        annotator.customTags = customTags;
        // push custom tags to the end of the list
        annotator.tokenCardStub.listOptions = annotator.tokenCardStub.listOptions.sort(function(it1, it2){
            var it1Cust = customTags.indexOf(it1) > -1,
                it2Cust = customTags.indexOf(it2) > -1;

            if(it1Cust && it2Cust) return 0;
            if(it1Cust) return 1;
            if(it2Cust) return -1;

            return 0;
        });

        if(annotatorLetter === 'B'){
            annotator.tokenCardStub.listOptions= annotator.tokenCardStub.listOptions
                .insertNullsAtPosition(
                    annotator.tokenCardStub.listOptions.length - customTags.length,
                    self.annotatorA.customTags.length);
            for(var i = 0; i <customTags.length; i++){
                self.annotatorA.tokenCardStub.appendTagOption(null);
            }
        }
        TokenCard.prototype.showListOptions.call(annotator.tokenCardStub);
    };

    /* Overriding parent functions */
    MorphoTaggerAgree.prototype.parent.saveDecision = function(){
      console.log('saving');
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

            // initializing annotators decisions
            var tokenHandle = self.tokenCards[i].activeTokenHandle;
            if(tokenHandle && self.tokenCards[i].isMainCard){
                self.that.showAnnotatorsDecisions(tokenHandle, taggerTags);
            }
        }
        self.currentTokenId = activeTokens[2].id.replace('an','');
    };

});