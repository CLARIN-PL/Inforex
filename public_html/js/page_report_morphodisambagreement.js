$(function () {

    MorphoTaggerAgree = function MorphoTaggerAgree(handleModule, handleTokens, tokensTags, editableSelect){
        MorphoTagger.call(this, handleModule, handleTokens, tokensTags, editableSelect);
    };
    MorphoTaggerAgree.prototype = Object.create(MorphoTagger.prototype);
    MorphoTaggerAgree.prototype.constructor = MorphoTaggerAgree;

    MorphoTaggerAgree.prototype.saveDecision = function(){
      console.log('saving');
    };
});