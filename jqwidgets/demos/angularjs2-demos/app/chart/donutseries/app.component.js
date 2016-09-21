System.register(['@angular/core', '../../../../../jqwidgets-ts/angular_jqxChart'], function(exports_1, context_1) {
    "use strict";
    var __moduleName = context_1 && context_1.id;
    var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
        var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
        if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
        else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
        return c > 3 && r && Object.defineProperty(target, key, r), r;
    };
    var __metadata = (this && this.__metadata) || function (k, v) {
        if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
    };
    var core_1, angular_jqxChart_1;
    var AppComponent;
    return {
        setters:[
            function (core_1_1) {
                core_1 = core_1_1;
            },
            function (angular_jqxChart_1_1) {
                angular_jqxChart_1 = angular_jqxChart_1_1;
            }],
        execute: function() {
            AppComponent = (function () {
                function AppComponent() {
                    this.flag = false;
                    this.data_source_mobile = {
                        datatype: "csv",
                        datafields: [
                            { name: 'Browser' },
                            { name: 'Share' }
                        ],
                        url: '../sampledata/mobile_browsers_share_dec2011.txt'
                    };
                    this.dataAdapter_mobile = new $.jqx.dataAdapter(this.data_source_mobile, { async: false, autoBind: true, loadError: function (xhr, status, error) { alert('Error loading "' + this.source.url + '" : ' + error); } });
                    this.data_source_desktop = {
                        datatype: "csv",
                        datafields: [
                            { name: 'Browser' },
                            { name: 'Share' }
                        ],
                        url: '../sampledata/desktop_browsers_share_dec2011.txt'
                    };
                    this.dataAdapter_desktop = new $.jqx.dataAdapter(this.data_source_desktop, { async: false, autoBind: true, loadError: function (xhr, status, error) { alert('Error loading "' + this.source.url + '" : ' + error); } });
                    this.settings = {
                        title: "Mobile & Desktop browsers share",
                        description: "(source: wikipedia.org)",
                        enableAnimations: true,
                        showLegend: true,
                        showBorderLine: true,
                        legendLayout: { left: 520, top: 170, width: 300, height: 200, flow: 'vertical' },
                        padding: { left: 5, top: 5, right: 5, bottom: 5 },
                        titlePadding: { left: 0, top: 0, right: 0, bottom: 10 },
                        seriesGroups: [
                            {
                                type: 'donut',
                                offsetX: 250,
                                source: this.dataAdapter_mobile,
                                xAxis: {
                                    formatSettings: { prefix: 'Mobile ' }
                                },
                                series: [
                                    {
                                        dataField: 'Share',
                                        displayText: 'Browser',
                                        labelRadius: 120,
                                        initialAngle: 10,
                                        radius: 130,
                                        innerRadius: 90,
                                        centerOffset: 0,
                                        formatSettings: { sufix: '%', decimalPlaces: 1 }
                                    }
                                ]
                            },
                            {
                                type: 'donut',
                                offsetX: 250,
                                source: this.dataAdapter_desktop,
                                colorScheme: 'scheme02',
                                xAxis: {
                                    formatSettings: { prefix: 'Desktop ' }
                                },
                                series: [
                                    {
                                        dataField: 'Share',
                                        displayText: 'Browser',
                                        labelRadius: 120,
                                        initialAngle: 10,
                                        radius: 70,
                                        innerRadius: 30,
                                        centerOffset: 0,
                                        formatSettings: { sufix: '%', decimalPlaces: 1 }
                                    }
                                ]
                            }
                        ]
                    };
                }
                AppComponent.prototype.ngAfterViewChecked = function () {
                    if (this.flag === false) {
                        this.Initialize();
                    }
                    this.flag = true;
                };
                AppComponent.prototype.Initialize = function () {
                    this.myChart.setOptions(this.settings);
                };
                __decorate([
                    core_1.ViewChild(angular_jqxChart_1.jqxChartComponent), 
                    __metadata('design:type', angular_jqxChart_1.jqxChartComponent)
                ], AppComponent.prototype, "myChart", void 0);
                AppComponent = __decorate([
                    core_1.Component({
                        selector: 'my-app',
                        template: '<angularChart width="850px" height="500px"></angularChart>',
                        directives: [angular_jqxChart_1.jqxChartComponent]
                    }), 
                    __metadata('design:paramtypes', [])
                ], AppComponent);
                return AppComponent;
            }());
            exports_1("AppComponent", AppComponent);
        }
    }
});
//# sourceMappingURL=app.component.js.map