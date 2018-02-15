$(document).on({
    mouseenter: function () {
        trIndex = $(this).index()+1;
        $("table.dataTable").each(function(index) {
            $(this).find("tr:eq("+trIndex+")").addClass("hover")
        });
    },
    mouseleave: function () {
        trIndex = $(this).index()+1;
        $("table.dataTable").each(function(index) {
            $(this).find("tr:eq("+trIndex+")").removeClass("hover")
        });
    }
}, ".dataTables_wrapper tr");


function MorphoAgreementPreview(reportsTable, diffTable, annotators, reportsGrouped, subcorp, usersMorphoDisamb){
	var self = this;
	self.$reportsTable = reportsTable;
	self.$diffTable = diffTable;
	self.annotators = annotators;
	self.reports = reportsGrouped;
	self.subcorp = subcorp;
	self.usersDisamb = usersMorphoDisamb;

	self.annotatorIds = annotators.map(function(it){return it.user_id});
    self.comparisonMode = 'base_ctag';

	self.init();
}

MorphoAgreementPreview.prototype.init = function(){
	var self = this;
	self.initDocsList();
};

MorphoAgreementPreview.prototype.showTokDiff = function(tok){
	var self = this;
	var data = {
		tok_range: tok.tok_range,
		orth: tok.orth,
		a: [],
		b: []
	};

	var getReadable = function(it){
		return $.map(it, function(it2){
			return '<b>' + it2.text + '</b> &emsp; ' + it2.ctag;
		});
	};

	if(tok.a && tok.a.length > 0)
		data.a = getReadable(tok.a);

	if(tok.b && tok.b.length > 0)
		data.b = getReadable(tok.b);

	if(data.a.length === 0 && data.b.length ===0)
		return;

	self.$diffTable
		.append(
			'<tr>' +
				'<td>' + data.tok_range + '</td>' +
				'<td>' + data.orth + '</td>' +
				'<td>' + data.a.join('<br>') + '</td>' +
				'<td>' + data.b.join('<br>') + '</td>' +
			'</tr>');
};

MorphoAgreementPreview.prototype.showReportDiff = function(data){
	var self = this;

	// todo- clear table
	self.$diffTable.find('tbody').empty();

	for(var report in data){
		for(var tok in data[report]){
			self.showTokDiff(data[report][tok])
		}
	}
};

MorphoAgreementPreview.prototype.compare = function(decisionA, decisionB){
	var self = this;
	// base and ctag comparison mode
	if(self.comparisonMode === 'base_ctag'){
		return (decisionA.base_text === decisionB.base_text &&
        decisionA.ctag === decisionB.ctag);
    } else{ // base only comparison mode
        return (decisionA.base_text === decisionB.base_text);
	}
};

MorphoAgreementPreview.prototype.initDocsList = function(){
	var self = this;

	var row;
	for(var i = 0; i < self.reports.length; i++){
        row = self.reports[i];
		self.$reportsTable.row.add( [
            row.id, row.title, row.total_tokens, row.divergent, row.PSA
        ] )
	}

	self.$reportsTable.on('click', 'tr', function(){
		// + for number casting, getting id from first element
		var id = +this.firstChild.innerHTML;
		var success = function(a,b){
			self.showReportDiff(a);
		};
        var error = function(a,b){
            console.log('error', a,b);
        };

        doAjax('get_users_morpho_agreement_decision',
			{
				annotator_a: self.annotatorIds[0],
				annotator_b: self.annotatorIds[1],
				report_ids: [id]
			}, success, error);
	});
	self.$reportsTable.draw();
};

