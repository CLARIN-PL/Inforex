/**
 * Part of the Inforex project
 */

function WidgetRelationGraphModal(modalSelector, panelSelector) {
    this.modal = $(modalSelector);
    this.panel = $(panelSelector);
    this.summary = this.modal.find(".report-relations-graph-summary");
    this.empty = this.modal.find(".report-relations-graph-empty");
    this.canvas = this.modal.find(".report-relations-graph-canvas");
    this.legend = this.modal.find(".report-relations-graph-legend");
    this.disabledMessage = "Graph view is unavailable because the relation list is disabled for this document.";
    this.width = 960;
    this.height = 620;
    this.activeRelationName = null;
    this.focusedRelationId = null;
    this.layoutMode = "compact";
    this.graph = null;
    this.simulation = null;
    this.svg = null;
    this.viewport = null;
    this.zoomBehavior = null;
    this.linkSelection = null;
    this.linkLabelSelection = null;
    this.nodeSelection = null;
    this.nodeTextSelection = null;
    this.annotationColorCache = {};
}

WidgetRelationGraphModal.prototype.init = function () {
    var parent = this;

    this.panel.find(".report-relations-graph-trigger").on("click", function (event) {
        event.preventDefault();
        parent.focusedRelationId = null;
        parent.render($(this).data("relation-list-disabled") === 1 || $(this).data("relation-list-disabled") === "1");
    });

    this.panel.find(".relation-graph-row").on("click", function () {
        parent.focusedRelationId = String($(this).data("relation-id") || "");
        parent.modal.modal("show");
        parent.render(false);
    });

    this.modal.find(".report-relations-graph-layout-switch .btn").on("click", function () {
        var button = $(this);
        parent.layoutMode = String(button.data("layout") || "compact");
        parent.modal.find(".report-relations-graph-layout-switch .btn").removeClass("is-active");
        button.addClass("is-active");
        if (parent.graph) {
            parent.restartSimulation();
        }
    });

    this.modal.on("hidden.bs.modal", function () {
        parent.activeRelationName = null;
        parent.focusedRelationId = null;
        parent.stopSimulation();
    });
};

WidgetRelationGraphModal.prototype.render = function (isDisabled) {
    this.resetView();

    if (isDisabled) {
        this.showEmpty(this.disabledMessage);
        return;
    }

    var relations = this.collectRelations();
    if (!relations.length) {
        this.showEmpty("No relations are available in this view.");
        return;
    }

    this.empty.hide().text("");
    this.canvas.show();

    this.graph = this.buildGraph(relations);
    this.summary.html(
        '<span class="report-relations-graph-summary-item"><strong>' + relations.length + '</strong> relations</span>' +
        '<span class="report-relations-graph-summary-item"><strong>' + this.graph.nodes.length + '</strong> annotations</span>' +
        '<span class="report-relations-graph-summary-item"><strong>' + Object.keys(this.graph.relationCounts).length + '</strong> relation types</span>'
    );

    this.renderLegend();
    this.drawGraph();
    this.restartSimulation();
};

WidgetRelationGraphModal.prototype.resetView = function () {
    this.stopSimulation();
    this.summary.empty();
    this.legend.empty();
    this.empty.hide().text("");
    this.canvas.empty().hide();
    this.activeRelationName = null;
    this.graph = null;
    this.svg = null;
    this.viewport = null;
    this.linkSelection = null;
    this.linkLabelSelection = null;
    this.nodeSelection = null;
    this.nodeTextSelection = null;
};

WidgetRelationGraphModal.prototype.showEmpty = function (message) {
    this.resetView();
    this.empty.text(message).show();
};

WidgetRelationGraphModal.prototype.stopSimulation = function () {
    if (this.simulation) {
        this.simulation.stop();
        this.simulation = null;
    }
};

WidgetRelationGraphModal.prototype.collectRelations = function () {
    var relations = [];
    var parent = this;

    this.panel.find(".relation-graph-row").each(function () {
        var row = $(this);
        var sourceSpan = row.find("td:eq(1) span").first();
        var targetSpan = row.find("td:eq(2) span").first();
        var sourceId = String(row.data("source-id") || "");
        var targetId = String(row.data("target-id") || "");
        var sourceDocumentSpan = parent.findDocumentAnnotationElement(sourceId);
        var targetDocumentSpan = parent.findDocumentAnnotationElement(targetId);

        relations.push({
            id: String(row.data("relation-id") || ""),
            name: String(row.data("relation-name") || ""),
            sourceId: sourceId,
            sourceType: String(row.data("source-type") || ""),
            sourceText: String(row.data("source-text") || ""),
            sourceColor: parent.readAnnotationColorFromElement(sourceDocumentSpan.length ? sourceDocumentSpan : sourceSpan),
            targetId: targetId,
            targetType: String(row.data("target-type") || ""),
            targetText: String(row.data("target-text") || ""),
            targetColor: parent.readAnnotationColorFromElement(targetDocumentSpan.length ? targetDocumentSpan : targetSpan)
        });
    });

    return relations;
};

WidgetRelationGraphModal.prototype.findDocumentAnnotationElement = function (annotationId) {
    if (!annotationId) {
        return $();
    }

    var selectors = [
        "#content #an" + annotationId,
        "#rp-content #an" + annotationId,
        ".report-preview-content-box #an" + annotationId,
        ".report-autoextension-document-content #an" + annotationId,
        ".contentBox #an" + annotationId,
        "span#an" + annotationId,
        "span[title^='an#" + annotationId + ":']"
    ];

    for (var i = 0; i < selectors.length; i++) {
        var match = $(selectors[i]).first();
        if (match.length) {
            return match;
        }
    }

    return $();
};

WidgetRelationGraphModal.prototype.buildGraph = function (relations) {
    var nodesByKey = {};
    var nodes = [];
    var links = [];
    var relationCounts = {};

    $.each(relations, function (_, relation) {
        var sourceKey = "an" + relation.sourceId;
        var targetKey = "an" + relation.targetId;

        if (!nodesByKey[sourceKey]) {
            nodesByKey[sourceKey] = {
                key: sourceKey,
                id: relation.sourceId,
                type: relation.sourceType,
                label: relation.sourceText,
                color: relation.sourceColor
            };
            nodes.push(nodesByKey[sourceKey]);
        }

        if (!nodesByKey[targetKey]) {
            nodesByKey[targetKey] = {
                key: targetKey,
                id: relation.targetId,
                type: relation.targetType,
                label: relation.targetText,
                color: relation.targetColor
            };
            nodes.push(nodesByKey[targetKey]);
        }

        relationCounts[relation.name] = (relationCounts[relation.name] || 0) + 1;

        links.push({
            id: relation.id,
            name: relation.name,
            source: sourceKey,
            target: targetKey,
            sourceId: relation.sourceId,
            targetId: relation.targetId,
            sourceText: relation.sourceText,
            targetText: relation.targetText
        });
    });

    return {
        nodes: nodes,
        links: links,
        relationCounts: relationCounts
    };
};

WidgetRelationGraphModal.prototype.drawGraph = function () {
    var parent = this;

    this.svg = d3.select(this.canvas[0])
        .attr("viewBox", "0 0 " + this.width + " " + this.height);

    this.svg.append("defs")
        .append("marker")
        .attr("id", "relation-graph-arrow")
        .attr("markerWidth", 10)
        .attr("markerHeight", 10)
        .attr("refX", 9)
        .attr("refY", 3)
        .attr("orient", "auto")
        .attr("markerUnits", "strokeWidth")
        .append("path")
        .attr("d", "M0,0 L0,6 L9,3 z")
        .attr("fill", "#5a7b88");

    this.viewport = this.svg.append("g").attr("class", "report-relations-graph-viewport");

    this.zoomBehavior = d3.zoom()
        .scaleExtent([0.6, 2.2])
        .on("zoom", function (event) {
            parent.viewport.attr("transform", event.transform);
        });

    this.svg.call(this.zoomBehavior);

    this.linkSelection = this.viewport.append("g")
        .attr("class", "report-relations-graph-links")
        .selectAll("path")
        .data(this.graph.links, function (d) { return d.id; })
        .enter()
        .append("path")
        .attr("class", "report-relations-graph-edge")
        .attr("marker-end", "url(#relation-graph-arrow)");

    this.linkSelection.append("title")
        .text(function (d) {
            return d.name + ": " + d.sourceText + " → " + d.targetText;
        });

    this.linkLabelSelection = this.viewport.append("g")
        .attr("class", "report-relations-graph-edge-labels")
        .selectAll("text")
        .data(this.graph.links, function (d) { return d.id; })
        .enter()
        .append("text")
        .attr("class", "report-relations-graph-edge-label")
        .text(function (d) { return d.name; });

    var nodeGroups = this.viewport.append("g")
        .attr("class", "report-relations-graph-nodes")
        .selectAll("g")
        .data(this.graph.nodes, function (d) { return d.key; })
        .enter()
        .append("g")
        .attr("class", "report-relations-graph-node")
        .on("mouseenter", function (_, d) {
            parent.highlightNode(d.key);
        })
        .on("mouseleave", function () {
            parent.clearHighlight();
        })
        .call(this.createDragBehavior());

    nodeGroups.append("circle")
        .attr("r", this.layoutMode === "expanded" ? 28 : 20)
        .style("fill", function (d) {
            return parent.getAnnotationColor(d).fill;
        })
        .style("stroke", function (d) {
            return parent.getAnnotationColor(d).stroke;
        });

    nodeGroups.append("text")
        .attr("class", "report-relations-graph-node-label")
        .attr("dy", 4)
        .style("fill", function (d) {
            return parent.getAnnotationColor(d).text;
        })
        .text(function (d) {
            return parent.truncate(d.label, parent.layoutMode === "expanded" ? 30 : 16);
        });

    nodeGroups.append("title")
        .text(function (d) {
            return "an#" + d.id + ": " + d.label + (d.type ? " (" + d.type + ")" : "");
        });

    this.nodeSelection = nodeGroups;
    this.nodeTextSelection = nodeGroups.select("text");
};

WidgetRelationGraphModal.prototype.createDragBehavior = function () {
    var parent = this;

    return d3.drag()
        .on("start", function (event, d) {
            if (!event.active && parent.simulation) {
                parent.simulation.alphaTarget(0.2).restart();
            }
            d.fx = d.x;
            d.fy = d.y;
        })
        .on("drag", function (event, d) {
            d.fx = event.x;
            d.fy = event.y;
        })
        .on("end", function (event, d) {
            if (!event.active && parent.simulation) {
                parent.simulation.alphaTarget(0);
            }
            d.fx = null;
            d.fy = null;
        });
};

WidgetRelationGraphModal.prototype.restartSimulation = function () {
    var parent = this;
    var chargeStrength = this.layoutMode === "expanded" ? -780 : -300;
    var linkDistance = this.layoutMode === "expanded" ? 170 : 74;
    var centerY = this.layoutMode === "expanded" ? this.height / 2 : (this.height / 2) + 4;

    if (!this.graph) {
        return;
    }

    this.stopSimulation();

    this.nodeSelection.select("circle").attr("r", this.layoutMode === "expanded" ? 28 : 20);
    this.nodeTextSelection.text(function (d) {
        return parent.truncate(d.label, parent.layoutMode === "expanded" ? 30 : 16);
    });

    this.simulation = d3.forceSimulation(this.graph.nodes)
        .force("link", d3.forceLink(this.graph.links).id(function (d) { return d.key; }).distance(linkDistance).strength(0.95))
        .force("charge", d3.forceManyBody().strength(chargeStrength))
        .force("center", d3.forceCenter(this.width / 2, centerY))
        .force("collision", d3.forceCollide().radius(this.layoutMode === "expanded" ? 42 : 24).iterations(2))
        .force("x", d3.forceX(this.width / 2).strength(this.layoutMode === "expanded" ? 0.035 : 0.06))
        .force("y", d3.forceY(centerY).strength(this.layoutMode === "expanded" ? 0.035 : 0.06))
        .on("tick", function () {
            parent.onTick();
        });

    this.applyLayoutZoom();
    this.applyActiveState();
};

WidgetRelationGraphModal.prototype.applyLayoutZoom = function () {
    if (!this.svg || !this.zoomBehavior) {
        return;
    }

    if (this.layoutMode === "compact") {
        var compactScale = 0.6;
        var compactTranslateX = (this.width - (this.width * compactScale)) / 2;
        var compactTranslateY = (this.height - (this.height * compactScale)) / 2;
        this.svg.transition()
            .duration(220)
            .call(
                this.zoomBehavior.transform,
                d3.zoomIdentity.translate(compactTranslateX, compactTranslateY).scale(compactScale)
            );
        return;
    }

    this.svg.transition()
        .duration(220)
        .call(this.zoomBehavior.transform, d3.zoomIdentity);
};

WidgetRelationGraphModal.prototype.onTick = function () {
    this.linkSelection.attr("d", function (d) {
        if (!d.source || !d.target) {
            return "";
        }

        if (d.source.key === d.target.key) {
            return [
                "M", d.source.x, d.source.y - 24,
                "C", d.source.x + 38, d.source.y - 64,
                d.source.x + 78, d.source.y - 22,
                d.source.x + 14, d.source.y + 4
            ].join(" ");
        }

        var dx = d.target.x - d.source.x;
        var dy = d.target.y - d.source.y;
        var distance = Math.sqrt(dx * dx + dy * dy) || 1;
        var offset = Math.min(26, distance * 0.12);
        var midX = (d.source.x + d.target.x) / 2;
        var midY = (d.source.y + d.target.y) / 2;
        var normalX = -dy / distance;
        var normalY = dx / distance;
        var curveX = midX + (normalX * offset);
        var curveY = midY + (normalY * offset);

        return "M " + d.source.x + " " + d.source.y + " Q " + curveX + " " + curveY + " " + d.target.x + " " + d.target.y;
    });

    this.linkLabelSelection
        .attr("x", function (d) {
            if (!d.source || !d.target) {
                return 0;
            }
            return d.source.key === d.target.key ? d.source.x + 42 : (d.source.x + d.target.x) / 2;
        })
        .attr("y", function (d) {
            if (!d.source || !d.target) {
                return 0;
            }
            return d.source.key === d.target.key ? d.source.y - 42 : ((d.source.y + d.target.y) / 2) - 8;
        });

    this.nodeSelection.attr("transform", function (d) {
        return "translate(" + d.x + "," + d.y + ")";
    });
};

WidgetRelationGraphModal.prototype.renderLegend = function () {
    var parent = this;
    var legend = [];

    $.each(Object.keys(this.graph.relationCounts).sort(), function (_, name) {
        legend.push(
            '<button type="button" class="report-relations-graph-legend-item" data-relation-name="' + parent.escapeXml(name) + '">' +
                '<span class="report-relations-graph-legend-name">' + parent.escapeXml(name) + '</span>' +
                '<span class="report-relations-graph-legend-count">' + parent.graph.relationCounts[name] + '</span>' +
            '</button>'
        );
    });

    this.legend.html(legend.join(""));

    this.legend.find(".report-relations-graph-legend-item").on("click", function () {
        var relationName = $(this).data("relation-name");
        parent.focusedRelationId = null;
        if (parent.activeRelationName === relationName) {
            parent.activeRelationName = null;
            parent.clearHighlight();
            return;
        }
        parent.activeRelationName = relationName;
        parent.applyRelationFilter(relationName);
    });
};

WidgetRelationGraphModal.prototype.highlightNode = function (nodeKey) {
    var connectedRelationNames = {};

    this.linkSelection
        .classed("is-active", function (d) {
            var isConnected = d.source.key === nodeKey || d.target.key === nodeKey;
            if (isConnected) {
                connectedRelationNames[d.name] = true;
            }
            return isConnected;
        })
        .classed("is-focus", false)
        .classed("is-muted", function (d) {
            return !(d.source.key === nodeKey || d.target.key === nodeKey);
        });

    this.linkLabelSelection
        .classed("is-active", function (d) {
            return d.source.key === nodeKey || d.target.key === nodeKey;
        })
        .classed("is-focus", false)
        .classed("is-muted", function (d) {
            return !(d.source.key === nodeKey || d.target.key === nodeKey);
        });

    this.nodeSelection
        .classed("is-active", function (d) { return d.key === nodeKey; })
        .classed("is-muted", function (d) { return d.key !== nodeKey; });

    this.legend.find(".report-relations-graph-legend-item").each(function () {
        var item = $(this);
        item.toggleClass("is-active", !!connectedRelationNames[item.data("relation-name")]);
        item.toggleClass("is-muted", !connectedRelationNames[item.data("relation-name")]);
    });
};

WidgetRelationGraphModal.prototype.clearHighlight = function () {
    if (this.activeRelationName || this.focusedRelationId) {
        this.applyActiveState();
        return;
    }

    this.linkSelection.classed("is-active", false).classed("is-focus", false).classed("is-muted", false);
    this.linkLabelSelection.classed("is-active", false).classed("is-focus", false).classed("is-muted", false);
    this.nodeSelection.classed("is-active", false).classed("is-muted", false);
    this.legend.find(".report-relations-graph-legend-item").removeClass("is-active is-muted");
};

WidgetRelationGraphModal.prototype.applyRelationFilter = function (relationName) {
    var activeNodes = {};

    this.linkSelection
        .classed("is-focus", false)
        .classed("is-active", function (d) {
            var isActive = d.name === relationName;
            if (isActive) {
                activeNodes[d.source.key] = true;
                activeNodes[d.target.key] = true;
            }
            return isActive;
        })
        .classed("is-muted", function (d) {
            return d.name !== relationName;
        });

    this.linkLabelSelection
        .classed("is-focus", false)
        .classed("is-active", function (d) {
            return d.name === relationName;
        })
        .classed("is-muted", function (d) {
            return d.name !== relationName;
        });

    this.nodeSelection
        .classed("is-active", function (d) { return !!activeNodes[d.key]; })
        .classed("is-muted", function (d) { return !activeNodes[d.key]; });

    this.legend.find(".report-relations-graph-legend-item").each(function () {
        var item = $(this);
        item.toggleClass("is-active", item.data("relation-name") === relationName);
        item.toggleClass("is-muted", item.data("relation-name") !== relationName);
    });
};

WidgetRelationGraphModal.prototype.applyFocusedRelation = function (relationId) {
    var activeNodes = {};

    this.linkSelection
        .classed("is-active", false)
        .classed("is-focus", function (d) {
            var isFocus = d.id === relationId;
            if (isFocus) {
                activeNodes[d.source.key] = true;
                activeNodes[d.target.key] = true;
            }
            return isFocus;
        })
        .classed("is-muted", function (d) {
            return d.id !== relationId;
        });

    this.linkLabelSelection
        .classed("is-active", false)
        .classed("is-focus", function (d) {
            return d.id === relationId;
        })
        .classed("is-muted", function (d) {
            return d.id !== relationId;
        });

    this.nodeSelection
        .classed("is-active", function (d) { return !!activeNodes[d.key]; })
        .classed("is-muted", function (d) { return !activeNodes[d.key]; });

    this.legend.find(".report-relations-graph-legend-item").removeClass("is-active is-muted");
};

WidgetRelationGraphModal.prototype.applyActiveState = function () {
    if (this.focusedRelationId) {
        this.applyFocusedRelation(this.focusedRelationId);
        return;
    }

    if (this.activeRelationName) {
        this.applyRelationFilter(this.activeRelationName);
        return;
    }

    this.clearHighlight();
};

WidgetRelationGraphModal.prototype.getAnnotationColor = function (node) {
    var annotationType = node && node.type ? node.type : "";

    if (node && node.color) {
        return node.color;
    }

    var cacheKey = annotationType || "__default__";
    var cached = this.annotationColorCache[cacheKey];
    if (cached) {
        return cached;
    }

    var probe = $("<span/>", {
        "class": "annotation " + (annotationType || "")
    }).css({
        position: "absolute",
        visibility: "hidden",
        left: "-9999px",
        top: "-9999px"
    }).text("x");

    $("body").append(probe);

    var computed = window.getComputedStyle(probe[0]);
    var fill = computed.backgroundColor;
    var text = computed.color;
    var border = computed.borderTopColor;

    probe.remove();

    var result = {
        fill: this.normalizeColor(fill, "#ffffff"),
        stroke: this.normalizeColor(border, this.normalizeColor(text, "#1f6f93")),
        text: this.normalizeColor(text, "#2d4a56")
    };

    if (result.fill === "rgba(0, 0, 0, 0)" || result.fill === "transparent") {
        result.fill = "#ffffff";
    }

    this.annotationColorCache[cacheKey] = result;
    return result;
};

WidgetRelationGraphModal.prototype.readAnnotationColorFromElement = function (element) {
    if (!element || !element.length) {
        return null;
    }

    var computed = window.getComputedStyle(element[0]);
    var fill = this.normalizeColor(computed.backgroundColor, "#ffffff");
    var text = this.normalizeColor(computed.color, "#2d4a56");
    var border = this.normalizeColor(computed.borderTopColor, text);

    if (fill === "rgba(0, 0, 0, 0)" || fill === "transparent") {
        fill = "#ffffff";
    }

    return {
        fill: fill,
        stroke: border,
        text: text
    };
};

WidgetRelationGraphModal.prototype.normalizeColor = function (value, fallback) {
    if (!value || value === "initial" || value === "inherit") {
        return fallback;
    }
    return value;
};

WidgetRelationGraphModal.prototype.truncate = function (text, length) {
    if (!text) {
        return "";
    }

    if (text.length <= length) {
        return text;
    }

    return text.substring(0, length - 1) + "…";
};

WidgetRelationGraphModal.prototype.escapeXml = function (text) {
    return String(text)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#39;");
};
