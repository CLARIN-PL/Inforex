{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}
<div style="margin: 0 auto">

	<div style="margin: 20px">
	    <div style="font-family: 'Chango', cursive; font-size: 40px; text-align: center">SEREL</div>
	    <div style="font-family: 'Shanti', sans-serif; font-size: 12px; text-align: center">Protyp systemu odpowiedzi na pytania <br/>o relacje semantyczne</div>
	</div>
	
	<div class="ui-state-highlight ui-corner-all" style="padding: 0pt 0.7em; margin: 10px; display: none"> 
	    <p style="padding: 10px"><span class="ui-icon ui-icon-info" style="float: left; margin-right: 0.3em;"></span>
	    Jest to prototyp systemu odpowiedzi na pytania o relacje semantyczne 8 kategorii zachodzące między obiektami reprezentowanymi przez nazwy własne.<br/><br/>
	    <b>Przykładowe pytania:</b>
	   <ul style="margin: 5px">
	      <li>Jakie miasta leżą w Polsce?</li>
	      <li>W jakich kraju znajduje się Nowy Jork?</li>
	   </ul>
	</p></div>
		
	<div id="box-question" style="background: #f0f0ff; padding: 10px;">
		<div style="margin: 5px; margin: 0 auto; width: 800px">
		    <div class="box-header" style="font-size: 16px; padding: 10px">Twoje pytanie:</div> 
		    <div class="box-content">
    			<input type="text" class="question {if $autosubmit}autosubmit{/if}" style="width: 500px; font-size: 16px; padding: 5px; font-weight: bold" value="{$question}"/>
	       		<input type="submit" class="buttonRun" style="padding: 5px" value="Wyślij"/>
	        </div>
		</div>
	</div>
    		
	<div id="box-interpretation" style="display: none; background: #EAFFEF; padding: 10px;">
	   <div style="margin: 0px auto; width: 800px">
	    <div style="font-size: 14px; font-weight: bold; ">
	       <div class="box-header">Interpretacja:</div> 
           <div style="font-size: 10px; padding: 6px; float: right">(<a href="#" class="show_hide_semql showItems" style="color: navy; text-decoration: underline">pokaż</a>)</div>
	       <div class="box-content question_description"></div>
	    </div>
		<div class="semquel_results" style="padding: 5px; background: #eee; display: none; margin: 10px">
			<table class="tablesorter" style="margin: 5px; width: 750px" cellspacing="1">
			    <tr>
			        <th>Pewność:</th>
			        <td class="measure"></td>
			    </tr>
			    <tr>
			        <th>Typ relacji:</th>
			        <td class="relation_type"></td>
			    </tr>
			    <tr>
			        <th>Podtyp relacji:</th>
			        <td class="relation_subtype"></td>
			    </tr>
			    <tr>
			        <th>Pytanie o typ obiektu:</th>
			        <td class="type"></td>
			    </tr>
			    <tr>
			        <th>Argument:</th>
			        <td class="argument"></td>
			    </tr>
			    <tr>
			        <th>Zapytanie SQL:</th>
			        <td class="sql_code"><code style="white-space: pre; overflow: scroll"></code></td>
			    </tr>
			</table>
		</div>
		</div>
	</div>
	
    <div id="box-answer"  style="display: none; background: #eee; padding: 10px;">
        <div style="margin: 0px auto; width: 800px">
            <div class="box-header">Odpowiedź:</div> 
            <div class="box-content" style="font-size: 12px; font-weight: normal; max-height: 200px; overflow: auto; background: white; border: 1px solid #ddd">
                <ol class="results_list" style="margin: 0px; padding: 0; padding-left: 30px">
                    <li><a href="">Jeden</a> (2)</li>
                    <li><a href="">Jeden</a> (2)</li>
                    <li><a href="">Jeden</a> (2)</li>
                </ol>
            </div>
        </div>  
     </div>

    <div id="box-context"  style="display: none; background: #FDFDF0; padding: 10px;">
        <div style="margin: 0px auto; width: 800px">
            <div class="box-header">Kontekst:</div>
            <div class="result_element_title" style="padding: 5px; margin-left: 125px; font-weight: bold; font-size: 14px"></div> 
            <div class="box-content answer-context" style="font-size: 12px; font-weight: normal">
            
            </div>
        </div>  
     </div>


    <div id="ajax-big" style="text-align: center; display: none;">
       <img src="gfx/ajax-big.gif" style="margin: 10px auto; "/>
    </div>
    	
</div>

<br/>
{include file="inc_footer.tpl"}