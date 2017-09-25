$(function () {

    MorphoTaggerAgree = function MorphoTaggerAgree(handleModule, handleTokens, tokensTags, editableSelect, decisionA, decisionB){
        // MorphoTagger.call(this, handleModule, handleTokens, tokensTags, editableSelect);
        // console.log(this.parent.constructor);
        console.log(this.parent.constructor(handleModule, handleTokens, tokensTags, editableSelect));
        console.log(this);
    };
    MorphoTaggerAgree.prototype.parent = Object.create(MorphoTagger.prototype);

    MorphoTaggerAgree.prototype.parent.saveDecision = function(){
      console.log('saving');
    };

});