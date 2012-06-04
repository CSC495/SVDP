jive.interactive.column = jive.interactive.column || {
    uuid: null,
    allColumns: {},
    allColumnsNo: 0,
    count: 0,
    ids: {},
    fontSizes:null,
    fonts: {
    	extension: null,
    	system: null
    },
    actions: {
        format: {icon: 'formatIcon', title: jive.i18n.get('column.format.title'), actions:{
            formatHeaderAndValues: {label: jive.i18n.get('column.format.formatmenu.label'), fn:'formatHeader'},
            hideColumn: {label: jive.i18n.get('column.format.hidecolumn.label'), fn:'hide', arg:'{"hide":true}'},
            showColumns: {label: jive.i18n.get('column.format.showcolumns.label'), actions:{
                showAll: {label: jive.i18n.get('column.format.showcolumns.all.label'), fn: 'hide', arg:'{"hide":false,"column":"all"}'}
            }}
        }},
        filter: {icon: 'filterIcon', title: jive.i18n.get('column.filter.title'), fn: 'filter'},
        sortAscending: {icon: 'sortAscIcon', title: jive.i18n.get('column.sortasc.title'), fn:'sort', arg:['Asc']},
        sortDescnding: {icon: 'sortDescIcon', title: jive.i18n.get('column.sortdesc.title'), fn: 'sort', arg:['Desc']}
    },
    dropColumns: {},
    dropPoints: {},
    visibleColumnsMoveData: {},
    ldi: null,
    rdi: null,
    delta: null,

    init: function(allColumns, tableUuid){
        var t,c,colData,lt,i,j,it = this;
        it.allColumns[tableUuid] = allColumns;
        /*
         * Load dynamic form data
         */
         it.formatHeaderForm.elements[0][1][0].groups = [{name: jive.i18n.get('column.dialog.extfonts'),values:[]}];
         it.formatCellsForm.elements[0][0][0].groups = [{name: jive.i18n.get('column.dialog.extfonts'),values:[]}];
         jQuery.each(it.fonts.extension,function(i,v) {
        	 it.formatHeaderForm.elements[0][1][0].groups[0].values.push([v,v]);
             it.formatCellsForm.elements[0][0][0].groups[0].values.push([v,v]);
         });
         it.formatHeaderForm.elements[0][1][0].groups.push({name: jive.i18n.get('column.dialog.sysfonts'),values:[]});
         it.formatCellsForm.elements[0][0][0].groups.push({name: jive.i18n.get('column.dialog.sysfonts'),values:[]});
         jQuery.each(it.fonts.system,function(i,v) {
        	 it.formatHeaderForm.elements[0][1][0].groups[1].values.push([v,v]);
        	 it.formatCellsForm.elements[0][0][0].groups[1].values.push([v,v]);
         });

        it.formatHeaderForm.elements[0][1][1].values = [];
        it.formatCellsForm.elements[0][0][1].values = [];
        jQuery.each(it.fontSizes,function(i,v) {
            it.formatHeaderForm.elements[0][1][1].values.push([v,v]);
            it.formatCellsForm.elements[0][0][1].values.push([v,v]);
        });
        /*
         * Compute drop boundaries (x-axis only) for DnD visual feedback.
         */
        it.dropPoints[tableUuid] = [];
        it.visibleColumnsMoveData[tableUuid] = [];
        it.dropColumns[tableUuid] = [];

        t = jQuery('.jrtableframe[data-uuid=' + tableUuid + ']').eq(0);
        t.find('.columnHeader').each(function(i){
            c = jQuery(this);
            lt = c.offset().left;
            colData = it.getColumnByUuid(c.data('popupid'), tableUuid);
            if (colData != null) {
            	colData.enabled = true;	// enable column
            }
            it.dropColumns[tableUuid].push('col_'+c.data('popupcolumn'));
            it.dropPoints[tableUuid].push(lt);
            it.visibleColumnsMoveData[tableUuid].push({
            	left: lt,
            	right: lt + c.width(),
            	width: c.width(),
            	index: colData != null ? colData.index : null,
            	uuid: c.data('popupid')
            });
        });
        it.dropPoints[tableUuid].push(lt + c.width());
        var markers = [];
        for(i=0;i<it.dropColumns[tableUuid].length;i++) {
            markers.push(it.dropPoints[tableUuid][i]);
            markers.push(it.dropPoints[tableUuid][i] + (it.dropPoints[tableUuid][i+1] - it.dropPoints[tableUuid][i]) / 2);
        }
        markers.push(it.dropPoints[tableUuid][i]);
        it.dropPoints[tableUuid] = markers;
        /*
         * Create show column menu
         */
        var menu = it.actions.format.actions.showColumns;
        for(i in allColumns) {
            c = allColumns[i];
           	menu.actions[c.uuid] = {label: c.label, fn:'hide', arg:'{"hide":false, "columnUuid": "' + c.uuid + '"}'};
           	it.allColumnsNo ++;
        }
        it.count = it.dropColumns[tableUuid].length;
        
        /*
         * Reset foobar
         */
        jasperreports.events.subscribeToEvent('jive.ui.foobar', 'jive.ui.foobar.reset', []);
    },
    getColumnByUuid: function(columnUuid, tableUuid) {
    	var tableColumns = this.allColumns[tableUuid],
    		colIdx;
    	
    	for (colIdx in tableColumns) {
    		if (tableColumns[colIdx].uuid === columnUuid) {
    			return tableColumns[colIdx];
    		}
    	}
    	return null;
    },
    getInteractiveElementFromProxy: function(cell){
        var clss = cell.attr('class').split(' ');
        return cell.parent().find('div[data-popupcolumn="' + clss[1].substring(4) + '"]');
    },
    getElementSize: function(){
        var jo = jive.selected.jo;
        var h;
        var cid = jo.data('popupColumn') || jo.data('popupcolumn');
        cid = ('' + cid).replace(/\./g,'\\.');
        var lastCell = jQuery('.col_' + cid + ':last', jo.closest('.jrtableframe'));
        if(lastCell && lastCell.length > 0) {
            var lastElemTop = lastCell.position().top;
            var lastElemHeight = lastCell.height();
            h = lastElemTop + lastElemHeight - jo.position().top;
        } else {
        	h = jo.height();
        }
        return {w:jo.width(),h:h};
    },
    onSelect: function(){
        var it = this,
        	allOption = [],
        	pmenu = jive.ui.foobar.menus.column.format.jo.find('ul[label="showColumns"]').eq(0),
        	tableUuid = jive.selected.jo.closest('.jrtableframe').data('uuid'),
        	allColumns = it.allColumns[tableUuid];

        if(it.count == 1) {
            jive.ui.foobar.menus.column.format.jo.find('li').eq(1).hide();
            jive.ui.overlay.jo.draggable('option','disabled',true);
        }else {
            jive.ui.foobar.menus.column.format.jo.find('li').eq(1).show();
            jive.ui.overlay.jo.draggable('option','disabled',false);
        }

        pmenu.children().each(function(i,el){
        	if (i > 0) {
        		var menuItmArgs = jQuery(el).data('args');
        		var col = it.getColumnByUuid(menuItmArgs.columnUuid, tableUuid);
        		if (col != null) {
        			menuItmArgs.column = [col.index];
        			jQuery(el).data('args', menuItmArgs);
        		}
        		if (col && col.enabled === true) {
        			el.style.display = 'none';
        		} else if (col){
        			allOption.push(col.index);
        			el.style.display = 'block';
        		} else {
        			el.style.display = 'none';
        		}
        	}
        });
        
        if(allOption.length > 0){
        	pmenu.parent().show();
            pmenu.children().eq(0).data('args',{hide:false,column:allOption});
        } else {
            pmenu.parent().hide();
        }
        
        // disable sort/filter if not sortable/filterable
        var foobarButtons = jive.ui.foobar.jo.find('button:gt(0)');
        !jive.selected.ie.filterdiv ? // FIXMEJIVE set boolean on template if can sort/filter
        	foobarButtons.addClass('disabled') :
        	foobarButtons.removeClass('disabled');
    },
    onDragStart: function(){
        var parent = jive.selected.jo.parent(), prop, i;
        this.uuid = parent.data('uuid');
        this.currentColumnsMoveData = this.visibleColumnsMoveData[this.uuid];
        this.currentColMoveData = null; 
        
        for (i = 0; i < this.currentColumnsMoveData.length; i++) {
        	if (this.currentColumnsMoveData[i].uuid === jive.selected.jo.data('popupid')) {
        		this.currentColMoveData = this.currentColumnsMoveData[i];
        		break;
        	}
        }
        
        var c = 'col_'+ jive.selected.jo.data('popupcolumn');
        var ci = jQuery.inArray(c,this.dropColumns[this.uuid]) * 2;
        this.ldi = ci == 0 ? 0 : ci - 1;
        this.rdi =  ci + 3 == this.dropPoints[this.uuid].length ? ci + 2 : ci + 3;

        this.delta = jive.ui.marker.position.left - this.dropPoints[this.uuid][ci+2];
        this.dropColumnIndex = ci;
        
        this.colToMoveToIndex = this.currentColMoveData.index;
    },
    onDrag: function(evt,ui){
        var ev = evt.originalEvent.originalEvent || evt;
        var markers = this.dropPoints[this.uuid];
        var i = 0, ln = this.currentColumnsMoveData.length, colMoveData, refColIndex, refColMiddle, isLeftToRight;

        if(evt.pageX < markers[this.ldi]) {
            if(this.ldi > 0){
                this.dropColumnIndex = this.ldi % 2 == 1 ? this.ldi - 1 : this.ldi;
                jive.ui.marker.jo.css('left', markers[this.dropColumnIndex] + this.delta +'px').show();
                this.rdi = this.ldi;
                this.ldi--;
            }
        }
        if(evt.pageX > markers[this.rdi]) {
            if(this.rdi < (markers.length-1)) {
                this.dropColumnIndex = this.rdi % 2 == 1 ? this.rdi + 1 : this.rdi;
                jive.ui.marker.jo.css('left', markers[this.dropColumnIndex] + this.delta + 'px').show();
                this.ldi = this.rdi;
                this.rdi++;
            }
        }
        
    	// determine move direction
    	if (evt.pageX > this.currentColMoveData.right) {
    		isLeftToRight = true;
    	} else if (evt.pageX < this.currentColMoveData.left){
    		isLeftToRight = false;
    	}
    	
    	// find column based on event.pageX
    	for (; i < ln; i++) {
    		colMoveData = this.currentColumnsMoveData[i];
    		if (evt.pageX <= colMoveData.right) {
    			refColIndex = parseInt(colMoveData.index);
    			refColMiddle = colMoveData.left + colMoveData.width / 2;
    			
    			if (evt.pageX <= refColMiddle) { // move left, relative to column middle
    				if (refColIndex > 0) {
    					if (isLeftToRight === true) {
    						this.colToMoveToIndex = refColIndex - 1;
    					} else if (isLeftToRight === false) {
    						this.colToMoveToIndex = refColIndex;
    					}
    				} else {
    					this.colToMoveToIndex = 0;
    				}
    			} else { // move right, relative to column middle
    				if (isLeftToRight === true) {
    					this.colToMoveToIndex = refColIndex;
    				} else if (isLeftToRight === false) {
    					this.colToMoveToIndex = refColIndex + 1;
    				}
    			}
    			break;
    		}
    	}
    	
    	if (isLeftToRight && this.colToMoveToIndex == null) {	// for maximum drag to right, move to index of last visible column
    		this.colToMoveToIndex = parseInt(this.currentColumnsMoveData[ln-1].index);
    	}
    },
    onDragStop: function(ev,ui){
    	if(this.colToMoveToIndex != null && this.colToMoveToIndex != jive.selected.ie.columnIndex) {
    		jive.runAction({
    			actionName: 'move',
    			moveColumnData: {
					tableUuid: this.uuid,
					columnToMoveIndex: jive.selected.ie.columnIndex,
					columnToMoveNewIndex: this.colToMoveToIndex
    			}
    		});
    	}
    },
    resize: function(width){
        var w = width < 8 ? 8 : Math.floor(width);
        jive.hide();
        jive.runAction({
            actionName: 'resize',
            resizeColumnData: {
                tableUuid: jive.selected.jo.parent('.jrtableframe').data('uuid'),
                columnIndex: jive.selected.ie.columnIndex,
                direction: 'right',
                width: w
            }
        });
    },
    sort: function(argv){
        jive.hide();
        jive.runAction(jive.selected.ie.headerToolbar['sort'+argv[0]+'Btn']['data-sortData']);
    },
    filter: function(){
        jive.ui.dialog.show(jive.i18n.get('column.filter.dialog.title'), ['columnfilter']);
    },
    formatHeader: function(){
        jive.ui.dialog.show(jive.i18n.get('column.format.dialog.title'), ['formatHeader', 'formatCells']);
    },
    hide: function(args){
    	var it = this, tableUuid = jive.selected.jo.parent('.jrtableframe').data('uuid');
        jive.hide();
        jive.runAction({
            actionName: 'hideUnhideColumns',
            columnData: {
                hide: args.hide,
                columnIndexes: args.column instanceof Array ? args.column : [jive.selected.ie.columnIndex],
                tableUuid: tableUuid
            }
        });
    },
    setDynamicProperties: function (obj) {
    	jive.interactive.column.fontSizes = obj.fontSizes;
    	jive.interactive.column.fonts = obj.fonts;
    },
    showCurrencyDropDown: function(){
    	jive.selected.form.inputs['currencyBtn1'].showOptions();
    },
    applyCurrencyFormat: function(args) {
    	var it = this,
    		input = jive.selected.form.inputs['currencyBtn1'],
    		cSymbol = jQuery('#formatPattern').data('cSymbol');
    	
		jQuery('#formatPattern').children().each(function (i, optElem) {
            var opt = jQuery(optElem);
            
            // attempt to remove current symbol, if present
            if (cSymbol && cSymbol.length > 0) {
            	opt.text(it.numberFormat.addRemoveCurrencySymbol(opt.text(), false, cSymbol));
            	opt.val(it.numberFormat.addRemoveCurrencySymbol(opt.val(), false, cSymbol));
            } 

            // apply new symbol
            if (args.val && args.val.length > 0) {
            	opt.text(it.numberFormat.addRemoveCurrencySymbol(opt.text(), true, args.val));
            	opt.val(it.numberFormat.addRemoveCurrencySymbol(opt.val(), true, args.val));
            	jQuery('#formatPattern').data('cSymbol', args.val);
            }
        });
    	
    	input.hideOptions();
    },
//    toggleCurrencyFormat: function(){
//        var it = this;
//        jQuery('#formatPattern').children().each(function (i, optElem) {
//            var opt = jQuery(optElem);
//            opt.text(it.numberFormat.addRemoveCurrencySymbol(opt.text(), jive.selected.form.inputs['currencyBtn'].selected));
//            opt.val(it.numberFormat.addRemoveCurrencySymbol(opt.val(), jive.selected.form.inputs['currencyBtn'].selected));
//        });
//    },
    togglePercentageFormat: function(){
        var it = this;
        jQuery('#formatPattern').children().each(function (i, optElem) {
            var opt = jQuery(optElem);
            opt.text(it.numberFormat.addRemovePercentageForNumber(opt.text(), jive.selected.form.inputs['percentageBtn'].selected));
            opt.val(it.numberFormat.addRemovePercentage(opt.val(), jive.selected.form.inputs['percentageBtn'].selected));
        });
    },
    toggleCommaFormat: function(){
        var it = this;
        jQuery('#formatPattern').children().each(function (i, optElem) {
            var opt = jQuery(optElem);
            opt.text(it.numberFormat.addRemoveThousandsSeparator(opt.text(), jive.selected.form.inputs['commaBtn'].selected));
            opt.val(it.numberFormat.addRemoveThousandsSeparator(opt.val(), jive.selected.form.inputs['commaBtn'].selected));
        });
    },
    addDecimal: function(){
        var it = this;
        jQuery('#formatPattern').children().each(function (i, optElem) {
            var opt = jQuery(optElem);
            opt.text(it.numberFormat.addRemoveDecimalPlace(opt.text(), true));
            opt.val(it.numberFormat.addRemoveDecimalPlace(opt.val(), true));
        });
    },
    remDecimal: function(){
        var it = this;
        jQuery('#formatPattern').children().each(function (i, optElem) {
            var opt = jQuery(optElem);
            opt.text(it.numberFormat.addRemoveDecimalPlace(opt.text(), false));
            opt.val(it.numberFormat.addRemoveDecimalPlace(opt.val(), false));
        });
    },
    numberFormat: {
        symbols: {
            currency: '\u00A4'
        },
        regex: {
            numberPart: /([\d|#]+(?!,))/,
            decimalPart: /(\.[\d|#]+)/,
            numericChar: /[\d|#]/
        },
        addRemoveDecimalPlace: (function () {
            return function (exp, booleanAdd) {
                var pozToken = exp.split(';')[0],
                    negToken = exp.split(';')[1];

                if (booleanAdd) {
                    exp = addDecimalPlaceToToken(pozToken);
                    if (negToken) {
                        exp = exp + ";" + addDecimalPlaceToToken(negToken);
                    }
                    return exp;
                } else {
                    exp = removeDecimalPlaceFromToken(pozToken);
                    if (negToken) {
                        exp = exp + ";" + removeDecimalPlaceFromToken(negToken);
                    }
                    return exp;
                }
            };

            function addDecimalPlaceToToken (token) {
                var dotIndex = token.indexOf('.');

                if (dotIndex != -1) { // already have decimals
                    var decimalPart = jive.interactive.column.numberFormat.regex.decimalPart.exec(token)[1];

                    return token.replace(decimalPart, decimalPart + '0');

                } else { // no decimals
                    var numberPart = jive.interactive.column.numberFormat.regex.numberPart.exec(token)[1];

                    return token.replace(numberPart, numberPart + '.0');
                }
            }

            function removeDecimalPlaceFromToken (token) {
                var result = token,
                    dotIndex = result.indexOf('.');

                if (dotIndex != -1) {
                    var decimalPart = jive.interactive.column.numberFormat.regex.decimalPart.exec(result)[1];

                    if (decimalPart.length > 2) {	// remove last decimal place
                        result = result.replace(decimalPart, decimalPart.substring(0, decimalPart.length - 1));
                    } else {	// remove all (dot and decimal place)
                        result = result.replace(decimalPart, '');
                    }
                }

                return result;
            }
        }()),

        addRemoveThousandsSeparator: (function () {
            return function (exp, booleanAdd) {
                var indexOfComma = exp.indexOf(','),
                    pozToken = exp.split(';')[0],
                    negToken = exp.split(';')[1];

                if (booleanAdd) {
                    if (indexOfComma == -1) {	// add
                        exp = addThousandsSeparatorToToken(pozToken);
                        if (negToken) {
                            exp = exp + ';' + addThousandsSeparatorToToken(negToken);
                        }
                    }
                } else {
                    if (indexOfComma != -1) {	// remove
                        exp = removeThousandsSeparatorFromToken(pozToken);
                        if (negToken) {
                            exp = exp + ';' + removeThousandsSeparatorFromToken(negToken);
                        }
                    }
                }
                return exp;
            };

            function addThousandsSeparatorToToken (token) {
                var indexOfNumericChar = token.indexOf(jive.interactive.column.numberFormat.regex.numericChar.exec(token)),
                    firstPart = token.substring(0, indexOfNumericChar + 1);

                return firstPart + ',' + token.substring(firstPart.length);;
            }

            function removeThousandsSeparatorFromToken (token) {
                return token.replace(',','');
            }
        }()),

        addRemovePercentage: (function () {
            return function (exp, booleanAdd) {
                var indexOfPercent = exp.indexOf('%'),
                    pozToken = exp.split(';')[0],
                    negToken = exp.split(';')[1];

                if (booleanAdd) {	// add
                    if (indexOfPercent == -1) {
                        exp = addPercentageToToken(pozToken);
                        if (negToken) {
                            exp = exp + ";" + addPercentageToToken(negToken);
                        }
                    }
                } else {	// remove
                    if (indexOfPercent != -1) {
                        exp = removePercentageFromToken(pozToken);
                        if (negToken) {
                            exp = exp + ";" + removePercentageFromToken(negToken);
                        }
                    }
                }
                return exp;
            };

            function addPercentageToToken (token) {
                return token + ' %';
            };

            function removePercentageFromToken (token) {
                return token.substring(0, token.length - 2);
            };
        }()),

        /**
         * @param negPattern must be in form of: pozSubPattern;negSubPattern
         */
        applyNegativeNumberPattern: function (negPattern) {
            var pozPatternRegex = new RegExp(negPattern.split(';')[0], 'g'),
                exp = jive.interactive.column.numberFormatExpression || '###0',
                pozToken = exp.split(';')[0];

            exp = negPattern.replace(pozPatternRegex, pozToken);
            return exp;
        },

        addRemovePercentageForNumber: function (numberExp, booleanAdd) {
            var numberPart = jive.interactive.column.numberFormat.regex.numberPart.exec(numberExp)[1];

            if (booleanAdd) {
                if (numberExp.indexOf('%') == -1 && numberPart.indexOf('00') == -1) {
                    numberExp = numberExp.replace(numberPart, numberPart + "00");
                    numberExp = numberExp + ' %';
                }
            } else {
                if (numberExp.indexOf('%') != -1 && numberPart.indexOf('00') != -1) {
                    numberExp = numberExp.replace(numberPart, numberPart.substring(0, numberPart.length - 2));
                    numberExp = numberExp.substring(0, numberExp.length - 2);
                }
            }

            return numberExp;
        },

        addRemoveCurrencySymbol: function(exp, booleanAdd, currencySymbol) {
            var cs = currencySymbol || jive.interactive.column.numberFormat.symbols.currency,
                indexOfCS = exp.indexOf(cs),
                pozToken = exp.split(';')[0],
                negToken = exp.split(';')[1];

            if (booleanAdd) {
                if (indexOfCS == -1) {
                    exp = cs + " " + pozToken;
                    if (negToken) {
                        exp = exp + ";" + cs + " " + negToken;
                    }
                }
            } else {
                if (indexOfCS != -1) {
                    exp = pozToken.substring(2);
                    if (negToken) {
                        exp = exp + ";" + negToken.substring(2);
                    }
                }
            }

            return exp;
        }
    }
}

jive.interactive.column.columnFilterForm = {
    name: 'columnfilter',
    method: 'get',
    jc: {},
    datePickerFormats: {
        "yyyy-MM-dd": {dateFormat: "yy-mm-dd"},
        "dd/MM/yyyy": {dateFormat: "dd/mm/yy"},
        "MM/dd/yyyy": {dateFormat: "mm/dd/yy"},
        "yyyy/MM/dd": {dateFormat: "yy/mm/dd"},
        "EEEEE dd MMMMM yyyy": {dateFormat: "DD dd MM yy"},
        "MMMMM dd. yyyy": {dateFormat: "MM dd. yy"},
        "dd/MM": {dateFormat: "dd/mm"},
        "dd/MM/yy": {dateFormat: "dd/mm/y"},
        "dd-MMM": {dateFormat: "dd-M"},
        "dd-MMM-yy": {dateFormat: "dd-M-y"},
        "MMM-yy": {dateFormat: "M-y"},
        "MMMMM-yy": {dateFormat: "MM-y"},
        "dd/MM/yyyy h.mm a": {dateFormat: "dd/mm/yy", timeFormat: "h.mm TT", ampm: true},
        "dd/MM/yyyy HH.mm.ss": {dateFormat: "dd/mm/yy", timeFormat: "hh.mm.ss"},
        "MMM": {dateFormat: "M"},
        "d/M/yyyy": {dateFormat: "d/m/yy"},
        "dd-MMM-yyyy": {dateFormat: "dd-M-yy"},
        "yyyy.MM.dd G 'at' HH:mm:ss z": {dateFormat: "yy.mm.dd", timeFormat:"hh:mm:ss", separator: 'at'},
        "EEE. MMM d. ''yy": {dateFormat: "D. M d. 'y"},
        "yyyy.MMMMM.dd GGG hh:mm aaa": {dateFormat: "yy.MM.dd AD", timeFormat:"hh:mm TT", ampm: true},
        "EEE. d MMM yyyy HH:mm:ss Z": {dateFormat: "D. d M yy", timeFormat:"hh:mm:ss z"},
        "yyMMddHHmmssZ": {dateFormat: "ymmdd", timeFormat:"hhmmssz"}
    },
    elements: [
        [[{type:'radio',id:'clearFilter',label: jive.i18n.get('column.filterform.clearfilter.true.label'), value:'true'}]],
        [
            [{type:'radio',id:'clearFilter',label: jive.i18n.get('column.filterform.clearfilter.false.label'), value:'false',colspan:4}],
            [
                {type:'list', id:'filterTypeOperator', values:[]},
                {type:'text', id:"fieldValueStart", value:''},
                {type:'label', value:'and'},
                {type:'text', id:"fieldValueEnd", value:''}
            ]
        ]
    ],
    onCreate:function(jo){
        /*
         *  This method is called when form is created. Can be used to initiate behavior and cache form elements.
         */
        var it = this;
        it.jc.filterStart = jQuery('#fieldValueStart');
        it.jc.filterEnd = jQuery('#fieldValueEnd').prop('disabled',true);
        it.jc.filterType = jQuery('#filterTypeOperator').change(function(){
            if(jQuery(this).val().indexOf('BETWEEN') >= 0){
                it.jc.filterEnd.parent().parent().prev().show();
                it.jc.filterEnd.parent().parent().show();
                it.jc.filterEnd.prop('disabled',false);
            } else {
                it.jc.filterEnd.parent().parent().prev().hide();
                it.jc.filterEnd.parent().parent().hide();
                it.jc.filterEnd.prop('disabled',true);
            }
        });
        jQuery('input[name="clearFilter"]').change(function(){
        	var filtertype = jive.selected.ie.filterdiv.filterDivForm.filterType.toLowerCase();
            
        	for(p in it.jc) (it.jc.hasOwnProperty(p) && jQuery(this).val() == 'true') ? it.jc[p].prop('disabled',true) : it.jc[p].prop('disabled',false);
            it.jc.filterEnd.prop('disabled', (it.jc.filterType.val().indexOf('BETWEEN') >= 0 && jQuery(this).val() == 'false') ? false : true);

            if (filtertype == 'boolean' && jQuery(this).val() == 'false') {
           		it.jc.filterStart.prop('disabled', true);
           		it.jc.filterStart.closest('td').hide();
            }
        });
    },
    onShow:function(){
        var it = this;
        var metadata = jive.selected.ie.filterdiv.filterDivForm;
        var filtertype = metadata.filterType.toLowerCase();
        var options = jive.selected.ie.filterdiv.filterOperatorTypeValueSelector || {
            text : [
                {key:'EQUALS',val:'Equals'},
                {key:'IS_NOT_EQUAL_TO',val:'Is not equal to'},
                {key:'CONTAINS',val:'Contains'},
                {key:'DOES_NOT_CONTAIN',val:'Does not contain'},
                {key:'STARTS_WITH',val:'Starts with'},
                {key:'DOES_NOT_START_WITH',val:'Does not start with'},
                {key:'ENDS_WITH',val:'Ends with'},
                {key:'DOES_NOT_END_WITH',val:'Does not end with'}
            ],
            date: [
                {key:'EQUALS',val:'Equals'},
                {key:'IS_NOT_EQUAL_TO',val:'Is not equal to'},
                {key:'IS_BETWEEN',val:'Is between'},
                {key:'IS_NOT_BETWEEN',val:'Is not between'},
                {key:'IS_ON_OR_BEFORE',val:'Is on or before'},
                {key:'IS_BEFORE',val:'Is before'},
                {key:'IS_ON_OR_AFTER',val:'Is on or after'},
                {key:'IS_AFTER',val:'Is after'}
            ],
            numeric: [
                {key:'EQUALS',val:'Equals'},
                {key:'DOES_NOT_EQUAL',val:'Does not equal'},
                {key:'GREATER_THAN',val:'Greater than'},
                {key:'GREATER_THAN_EQUAL_TO',val:'Greater than or equal to'},
                {key:'LESS_THAN',val:'Less than'},
                {key:'LESS_THAN_EQUAL_TO',val:'Less than or equal to'},
                {key:'IS_BETWEEN',val:'Is between'},
                {key:'IS_NOT_BETWEEN',val:'Is not between'}
            ],
            boolean: [
                {key:'IS_TRUE', val:'Is true'},
                {key:'IS_NOT_TRUE', val:'Is not true'},
                {key:'IS_FALSE', val:'Is false'},
                {key:'IS_NOT_FALSE', val:'Is not false'}
            ]
        }[filtertype];

        it.jc.filterType.empty();
        var htm = [];
        jQuery.each(options,function(k,v){
            v && htm.push('<option value="'+v.key+'">'+v.val+'</option>');
        });
        it.jc.filterType.append(htm.join(''));
        it.jc.filterType.val(metadata.filterTypeOperator);
        it.jc.filterStart.val(metadata.fieldValueStart);

        var filterOff = metadata.filterTypeOperator == '' ? true : false;
        jQuery('input[name="clearFilter"][value="'+filterOff+'"]').prop("checked",true);
        for(p in it.jc) (it.jc.hasOwnProperty(p) && filterOff) ? it.jc[p].prop('disabled',true) : it.jc[p].prop('disabled',false);

        if(!filterOff && metadata.filterTypeOperator.indexOf('BETWEEN') >= 0) {
            it.jc.filterEnd.parent().parent().prev().show();
            it.jc.filterEnd.parent().parent().show();
            it.jc.filterEnd.val(metadata.fieldValueEnd).prop('disabled', false);
        } else {
            it.jc.filterEnd.parent().parent().prev().hide();
            it.jc.filterEnd.parent().parent().hide();
            it.jc.filterEnd.val(metadata.fieldValueEnd).prop('disabled', true);
        }

        if (filtertype === 'boolean') {
			it.jc.filterStart.prop('disabled',true);
			it.jc.filterStart.closest('td').hide();
        } else {
			it.jc.filterStart.closest('td').show();
        }
        if(filtertype === 'date') {
            var pickerOptions = {
                changeMonth: true,
                changeYear: true,
                dateFormat: metadata.calendarPattern
            }
            it.jc.filterStart.datepicker(pickerOptions);
            it.jc.filterEnd.datepicker(pickerOptions);
        } else {
            it.jc.filterStart.datepicker('destroy');
            it.jc.filterEnd.datepicker('destroy');
        }
    },
    submit:function(){
        var metadata = jive.selected.ie.filterdiv.filterDivForm,
            actionData = {actionName: 'filter'};

        actionData.filterData = {
            tableUuid: metadata.tableUuid,
            fieldName: metadata.fieldName,
            filterType: metadata.filterType,
            filterPattern: metadata.filterPattern,
            fieldValueStart: jive.selected.form.jo.find('input[name="fieldValueStart"]').val(),
            filterTypeOperator: jive.selected.form.jo.find('select[name="filterTypeOperator"]').val(),
            clearFilter: jive.selected.form.jo.find('input[name="clearFilter"]:checked').val()
        };

        if(!jive.selected.form.jo.find('input[name="filterValueEnd"]').is(':hidden')){
            actionData.filterData.fieldValueEnd = jive.selected.form.jo.find('input[name="fieldValueEnd"]').val();
        }

        if(jive.selected.ie.filterPattern){
            actionData.filterData.filterPattern = metadata.filterPattern;
        }

        jive.hide();
        jive.runAction(actionData);
    }
};
jasperreports.events.subscribeToEvent('jive.init', 'jive.ui.forms.add', [jive.interactive.column.columnFilterForm]);

jive.interactive.column.formatHeaderForm = {
	actionDataCache: {},
    title: jive.i18n.get('column.formatHeaderForm.title'),
    name: 'formatHeader',
    method: 'get',
    elements: [[
        [{type:'text', id:'headingName', label: jive.i18n.get('column.formatHeaderForm.headingName.label'), value:'',colspan:4}],
        [
            {type:'list', id:'headerFontName', label: jive.i18n.get('column.formatforms.fontName.label'), values:[], freeText: true, size:6, rowspan:2},
            {type:'list', id:'headerFontSize', label: jive.i18n.get('column.formatforms.fontSize.label'), values:[], freeText: true, size:6, rowspan:2},
            {
                type: 'buttons',
                label: jive.i18n.get('column.formatforms.styleButtons.label'),
                items: [
                    {type:'checkbox',id:'headerFontBold',value:'bold',bIcon:'boldIcon'},
                    {type:'checkbox',id:'headerFontItalic',value:'italic',bIcon:'italicIcon'},
                    {type:'checkbox',id:'headerFontUnderline',value:'underline',bIcon:'underlineIcon'}
                ]
            },
            {
                type: 'buttons',
                label:'Color',
                items: [
                    {type:'color',id:'headerFontColor',bIcon:'fontColorIcon',title:jive.i18n.get('column.formatforms.fontColor.title'), drop: true}
                ]
            }
        ],
        [
            {
                type: 'buttons',
                label:'Alignment',
                items: [
                    {type:'radio',id:'headerFontAlign',value:'Left',bIcon:'leftIcon'},
                    {type:'radio',id:'headerFontAlign',value:'Center',bIcon:'centerIcon'},
                    {type:'radio',id:'headerFontAlign',value:'Right',bIcon:'rightIcon'}
                ]
            }
        ]
    ]],
    onCreate:function(jo){

    },
    onShow:function(){
        var metadata,
        	inputs = jive.selected.form.inputs;
        
        if (this.actionDataCache[this.name]) {
        	metadata = this.actionDataCache[this.name].editColumnHeaderData;
        } else {
        	metadata = jive.selected.ie.headingsTabContent;
        }

        metadata.fontBold ? inputs['headerFontBold'].set() : inputs['headerFontBold'].unset();
        metadata.fontItalic ?  inputs['headerFontItalic'].set() : inputs['headerFontItalic'].unset();
        metadata.fontUnderline ?  inputs['headerFontUnderline'].set() : inputs['headerFontUnderline'].unset();

        inputs['headerFontAlign'].set(metadata.fontHAlign);
        inputs['headingName'].set(metadata.headingName);
        inputs['headerFontName'].set(metadata.fontName);
        inputs['headerFontSize'].set(metadata.fontSize);
        inputs['headerFontColor'].set(metadata.fontColor);
    },
    submit:function(){
    	var actions = [],
    		prop;
    	this.actionDataCache[this.name] = this.getActionData();
    	
    	for (prop in this.actionDataCache) {
    		if (this.actionDataCache.hasOwnProperty(prop)) {
    			actions.push(this.actionDataCache[prop]);
    		}
    	}
    	
    	jive.hide();
        jive.runAction(actions);
    },
    onBlur: function() {
        this.actionDataCache[this.name] = this.getActionData();
    },
    onHide: function() {
        this.actionDataCache = {};
    },
    getActionData: function() {
    	var inputs = jive.selected.form.inputs;
    	return {
            actionName: 'editColumnHeader',
            editColumnHeaderData: {
                tableUuid: jive.selected.jo.parent('.jrtableframe').data('uuid'),
                columnIndex: jive.selected.ie.columnIndex,
                headingName: inputs['headingName'].get(),
                fontName: inputs['headerFontName'].get(),
                fontSize: inputs['headerFontSize'].get(),
                fontBold: inputs['headerFontBold'].get(),
                fontItalic: inputs['headerFontItalic'].get(),
                fontUnderline: inputs['headerFontUnderline'].get(),
                fontHAlign: inputs['headerFontAlign'].get(),
                fontColor: inputs['headerFontColor'].get()
            }
    	}
    }
};
jasperreports.events.subscribeToEvent('jive.init', 'jive.ui.forms.add', [jive.interactive.column.formatHeaderForm]);

jive.interactive.column.formatCellsForm = {
	actionDataCache: {},
    title: jive.i18n.get('column.formatCellsForm.title'),
    name: 'formatCells',
    method: 'get',
    elements: [[
        [
            {type:'list', id:'cellsFontName', label: jive.i18n.get('column.formatforms.fontName.label'), values:[], freeText: true, size:6, rowspan:2},
            {type:'list', id:'cellsFontSize', label: jive.i18n.get('column.formatforms.fontSize.label'), values:[], freeText: true, size:6, rowspan:2},
            {
                type: 'buttons',
                label: jive.i18n.get('column.formatforms.styleButtons.label'),
                items: [
                    {type:'checkbox',id:'cellsFontBold',value:'bold',bIcon:'boldIcon'},
                    {type:'checkbox',id:'cellsFontItalic',value:'italic',bIcon:'italicIcon'},
                    {type:'checkbox',id:'cellsFontUnderline',value:'underline',bIcon:'underlineIcon'}
                ]
            },
            {
                type: 'buttons',
                label:'Color',
                items: [
                    {type:'color',id:'cellsFontColor',bIcon:'fontColorIcon',title:jive.i18n.get('column.formatforms.fontColor.title'), drop: true}
                ]
            }
        ],
        [
            {
                type: 'buttons',
                label:'Alignment',
                items: [
                    {type:'radio',id:'cellsFontAlign',value:'Left',bIcon:'leftIcon'},
                    {type:'radio',id:'cellsFontAlign',value:'Center',bIcon:'centerIcon'},
                    {type:'radio',id:'cellsFontAlign',value:'Right',bIcon:'rightIcon'}
                ]
            }
        ],
        [
            {type:'list',id:'formatPattern',label: jive.i18n.get('column.formatCellsForm.formatPattern.label'),freeText:true,values:[],colspan:2, size:4, rowspan:2},
            {
                type:'buttons',
                id: 'numberFormatButtons',
                label: 'Number Format',
                items: [
//                  {type:'checkbox',id:'currencyBtn',fn:'toggleCurrencyFormat',value:'',bIcon:'currencyIcon'},
                    {type:'checkbox',id:'percentageBtn',fn:'togglePercentageFormat',value:'',bIcon:'percentageIcon'},
                    {type:'checkbox',id:'commaBtn',fn:'toggleCommaFormat',value:'',bIcon:'commaIcon'},
                    {type:'action',id:'increaseDecimalsBtn',fn:'addDecimal',value:'',bIcon:'increaseDecimalsIcon'},
                    {type:'action',id:'decreaseDecimalsBtn',fn:'remDecimal',value:'',bIcon:'decreaseDecimalsIcon'}
                ]
            }
        ],
        [
            {
                type:'buttons',
                label: 'Currency',
                items: [
                    {
                        type: 'dropdown',
                        drop: true,
                        id: 'currencyBtn1',
                        fn: 'showCurrencyDropDown',
                        bIcon: 'currencyIcon',
                        options: {
                            'none': {label: '&lt;None&gt;', value: '', fn: 'applyCurrencyFormat'},
                            'locale_specific': {label: jive.i18n.get('column.formatCellsForm.numberFormatButtons.localespecific.label'), value: '\u00A4', fn: 'applyCurrencyFormat'},
                            'dollar': {label: '\u0024 - USD', value: '\u0024', fn: 'applyCurrencyFormat'},
                            'euro': {label: '\u20AC - EUR', value: '\u20AC', fn: 'applyCurrencyFormat'},
                            'pound': {label: '\u00A3 - GBP', value: '\u00A3', fn: 'applyCurrencyFormat'},
                            'yen': {label: '\u00A5 - YEN', value: '\u00A5', fn: 'applyCurrencyFormat'}
                        }
                    }
                ]
            }
        ]
    ]],
    onCreate:function(jo){

    },
    onShow:function(){
        var metadata,
            inputs = jive.selected.form.inputs,
            htm = [],
            ie = jive.selected.ie,
            jo = jive.selected.form.jo;
        
        if (this.actionDataCache[this.name]) {
        	metadata = this.actionDataCache[this.name].editColumnValueData;
        } else {
        	metadata = jive.selected.ie.valuesTabContent;
        }

        metadata.fontBold ? inputs['cellsFontBold'].set() : inputs['cellsFontBold'].unset();
        metadata.fontItalic ?  inputs['cellsFontItalic'].set() : inputs['cellsFontItalic'].unset();
        metadata.fontUnderline ?  inputs['cellsFontUnderline'].set() : inputs['cellsFontUnderline'].unset();

        inputs['cellsFontAlign'].set(metadata.fontHAlign);
        inputs['cellsFontName'].set(metadata.fontName);
        inputs['cellsFontSize'].set(metadata.fontSize);
        inputs['cellsFontColor'].set(metadata.fontColor);

        if(typeof ie.formatPatternLabel === 'string') {
            jQuery.each(ie.formatPatternSelector,function(i,o){
                o && htm.push('<option value="'+o.key+'">'+o.val+'</option>');
            })
            jQuery('#formatPattern').html(htm.join(''));
            inputs['formatPattern'].set(metadata.formatPattern);
            jo.find('tr:gt(1)').show();
            if (ie.formatPatternLabel.indexOf('Number') >= 0) {
                jo.find('tr:eq(2)').children('td:last').css('visibility','visible');
                jo.find('tr:eq(3)').children('td:last').css('visibility','visible');
            	inputs['percentageBtn'].unset();
            	inputs['commaBtn'].unset();
            } else {
                jo.find('tr:eq(2)').children('td:last').css('visibility','hidden');
                jo.find('tr:eq(3)').children('td:last').css('visibility','hidden');
            }
        } else {
            jo.find('tr:gt(1)').hide();
        }
    },
    submit: function(){
    	var actions = [],
			prop;
		this.actionDataCache[this.name] = this.getActionData();
		
		for (prop in this.actionDataCache) {
			if (this.actionDataCache.hasOwnProperty(prop)) {
				actions.push(this.actionDataCache[prop]);
			}
		}
		
		jive.hide();
	    jive.runAction(actions);
	    this.actionDataCache = {};
    },
    onBlur: function() {
        this.actionDataCache[this.name] = this.getActionData();
    },
    onHide: function() {
        this.actionDataCache = {};
    },
    getActionData: function() {
    	var inputs = jive.selected.form.inputs;
		return {
            actionName: 'editColumnValues',
            editColumnValueData:{
                tableUuid: jive.selected.jo.parent('.jrtableframe').data('uuid'),
                columnIndex: jive.selected.ie.columnIndex,
                fontName: inputs['cellsFontName'].get(),
                fontSize: inputs['cellsFontSize'].get(),
                fontBold: inputs['cellsFontBold'].get(),
                fontItalic: inputs['cellsFontItalic'].get(),
                fontUnderline: inputs['cellsFontUnderline'].get(),
                fontHAlign: inputs['cellsFontAlign'].get(),
                fontColor: inputs['cellsFontColor'].get(),
                formatPattern: inputs['formatPattern'].get()
            }
		};
    }
};
jasperreports.events.subscribeToEvent('jive.init', 'jive.ui.forms.add', [jive.interactive.column.formatCellsForm]);

jasperreports.events.registerEvent('jive.interactive.column.init').trigger();
