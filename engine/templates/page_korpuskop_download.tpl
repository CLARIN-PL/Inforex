{include file="inc_header2.tpl"}
<div class="panel panel-danger administration-content-panel">
    <div class="panel-heading administration-content-heading">
        <span class="administration-content-heading-icon"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>
        Korpuskop download error
    </div>
    <div class="panel-body">
        <p>Nie udało się odnaleźć pliku raportu Korpuskop.</p>
        <p><strong>Plik:</strong> {$file|escape}</p>
        {if $run.run_id}
            <p><strong>Run ID:</strong> {$run.run_id|escape}</p>
        {/if}
        <p><a href="index.php?page=corpus_korpuskop&amp;corpus={$corpus.id}" class="btn btn-default"><i class="fa fa-arrow-left" aria-hidden="true"></i> Wróć do Korpuskop</a></p>
    </div>
</div>
