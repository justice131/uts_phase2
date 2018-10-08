<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <title>Data Insight</title>
        <?php include("../common.scripts/all_import_scripts.html"); ?>
        <?php include("../common.scripts/pc_import_scripts.html"); ?>
        <script src="../border/MacquarieBogan_watersource_centroids.geojson"></script>
    </head>
    <body style="background-color:#F3F3F4;">
        <?php include("../common.scripts/navigator.html"); ?>
	<div id="page-wrapper" class="gray-bg dashboard"  style="padding-bottom:20px">
            <div class="row" style="width: 6500px;">
                <div id="left_panel" class="box-container" style="width:5.5%;">
                    <table style="width:100%">
                        <tr>
                            <td>
                                <div id="setting">
                                    <div class="box-title">
                                            <h4><b>Catchment and Scenario Settings</b></h4>
                                    </div>
                                    <div class="box-content" style="height:40%;">
                                        <h5><b>Select a Catchment for More Information</b></h5>
                                        <select id="select_catchment" style="width:135px" onchange="scenario_selection()">
                                            <option value="default">-----CATCHMENT-----</option>
                                            <option value="MacquarieBogan">Macquarie</option>
                                            <option value="ManningRiver">Manning</option>
                                        </select><br/><br/>
                                        <h5><b>Select the scenario you want to explore</b></h5>
                                        <table>
                                          <tr>
                                                <th>
                                                    <select id="select_scenario" style="width:300px" onchange="scenario_selection()">
                                                        <option value="default">------------------------Scenario------------------------</option>
                                                        <option value="potential_need">Potential need for new infrastructure</option>
                                                        <option value="water_risk">Water related health risk due to poor ecosystem health</option>
                                                    </select>
                                                </th>
                                                <th>
                                                </th>
                                          </tr>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div id="legend_title" class="box-title">
                                      <h4><b>Map Icon Legend</b></h4>
                                </div>
                                <div id="legend_content" class="box-content">
                                    <div id="rightdiv">
                                        <div id="legend">
                                            <br>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div id="map_panel" class="box-container" style="width:20%;" >
                    <div class="box">
                        <div class="box-title">
                            <div id="s0_title">
                            </div>
                        </div>
                        <div class="box-content" role="tabpanel">
                                <div id="map"></div>
                        </div>
                    </div>
                </div>
                <div id="pc_panel" class="box-container" style="width:44.5%;">
                    <div class="box">
                            <div class="box-title">
                                <h4><b>Parallel Coordinates</b></h4>                                                
                            </div>
                        <div class="box-content" role="tabpanel">
                            <div id="parrallel_coordinate" class="parcoords"></div>
                        </div>
                    </div>
                </div>
                <div id="data_table_panel" class="box-container" style="width:30%;">
                    <div class="box">
                        <div class="box-title">
                            <h4><b>Water Source List</b></h4>                                               
                        </div>
                        <div class="box-content">
                            <div id="grid"></div>
                        </div>
                    </div>
                </div>                   
            </div>
	</div>
        <div class="se-pre-con"></div>
        
        <script type="text/javascript">
            window.onload=function(){
                pageHeight = document.body.scrollHeight;//获取页面高度
                document.getElementById("map").style.height = (pageHeight-100) + "px";//设置map高度
                document.getElementById("parrallel_coordinate").style.height = (pageHeight-100) + "px";//设置pararell coordinate高度
                document.getElementById("grid").style.height = (pageHeight-100) + "px";//设置数据表格高度
                var settingHeight = document.getElementById("setting").offsetHeight;//获取设置高度
                var legendTitleHeight = document.getElementById("legend_title").offsetHeight;//获取map legend title高度
                document.getElementById("legend_content").style.height = (pageHeight - settingHeight - legendTitleHeight - 16) + "px";//设置map legend content高度
                document.getElementById('map').style.visibility="hidden";
            }
            
            $(window).load(function() {// Show preloader
                $(".se-pre-con").fadeOut("slow");;
            });
            document.getElementById('s0_title').innerHTML = '<span style="font-size:18px; font-weight:bold; margin-bottom: 0; height: 48px;">'+'Water Source'+'</span>';
            
            var removeLayer = function (feature) {
                for (var i = 0; i < feature.length; i++){     
                    map.removeLayer(feature[i]);
                }
            };
            
            /*Functions section*/
            function showIt(d) {
                return d > -1 ? 0.75 : 0;
            }

            function resetHighlight(e) {
                geojson.resetStyle(e.target);
                //info.update();
            }

            function zoomToFeature(e) {
                var layer = e.target;
                info.update(layer.feature.properties);
                //map.fitBounds(e.target.getBounds());
            }

            function onEachFeature(feature, layer) {
                layer.on({
                        mouseover: highlightFeature,
                        mouseout: resetHighlight,
                        click: zoomToFeature
                });
            }

            function highlightFeature(e) {
                var layer = e.target;
                if (layer._layers) {
                        console.log(layer);
                        layer.eachLayer(function (myLayer) {
                                console.log(myLayer);
                                myLayer.setStyle({
                                        weight: myLayer.options ? (myLayer.options.opacity > 0 ? 3 : 0) : 0,
                                        color: '#666',
                                        dashArray: '',
                                });
                        });
                } else {
                        layer.setStyle({
                                weight: layer.options ? (layer.options.opacity > 0 ? 3 : 0) : 0,
                                color: '#666',
                                dashArray: '',
                        });
                }

                if (!L.Browser.ie && !L.Browser.opera) {
                        layer.bringToFront();
                }
            }
            
            /*Function section*/
            function toThousands(num) {
                if (isNaN(num)) { 
                    throw new TypeError("num is not a number"); 
                } 
                var groups = (/([\-\+]?)(\d*)(\.\d+)?/g).exec("" + num), 
                    mask = groups[1],
                    integers = (groups[2] || "").split(""),
                    decimal = groups[3] || "",
                    remain = integers.length % 3; 
  
                var temp = integers.reduce(function(previousValue, currentValue, index) { 
                if (index + 1 === remain || (index + 1 - remain) % 3 === 0) { 
                    return previousValue + currentValue + ","; 
                } else { 
                    return previousValue + currentValue; 
                } 
                }, "").replace(/\,$/g, ""); 
                    return mask + temp + decimal; 
            }
            
            //scenario selection onchange function
            function scenario_selection(){
                var cm_ele=document.getElementById("select_catchment");
                var cm_index=cm_ele.selectedIndex;
                var cm=cm_ele.options[cm_index].value;
                
                var scenario_ele=document.getElementById("select_scenario");
                var scenario_index=scenario_ele.selectedIndex ;
                var scenario=scenario_ele.options[scenario_index].value;
                if(cm=="MacquarieBogan"){
                    if(scenario=="potential_need"){
                        show_s10('s10');
                    }else if(scenario=="water_risk"){
                        show_s11('s11');
                    }
                }else if(cm=="ManningRiver"){
                    clear_all_layers();
                    if(scenario=="potential_need"){
                        alert("There are no scenarios for ManningRiver currently.");
                    }else if(scenario=="water_risk"){
                        alert("There are no scenarios for ManningRiver currently.");
                    }
                }else{
                    alert("Please select a catchment to explore.");
                }
            }
            
            //Clear the layer
            function clear_all_layers(){
                document.getElementById('s0_title').innerHTML = '<span style="font-size:18px; font-weight:bold; margin-bottom: 0; height: 48px;">'+'Water Source'+'</span>';
                document.getElementById('parrallel_coordinate').innerHTML = "";
                document.getElementById('grid').innerHTML = "";
                if (typeof info != 'undefined'){
                    map.removeControl(info);
                }
                if (typeof legend != 'undefined'){
                    map.removeControl(legend);
                }
                removeLayer(displayed_s10);
                removeLayer(displayed_s11);
            }

            /*overall variables*/
            var myCols = [
                '#ff3333',//Red
                '#ff8533',//Orange
                '#33ff33'//Green
            ];

            var lgas = MacquarieBogan_unregulated;
            var padding = 35;
            var layer, overlay;
            var filtered;
            var isSelected = false;
            var lga = new Array();
            
            /*overall variables*/
            var map = L.map('map',{zoomControl: false}).setView([-29.0, 134.7], 4.4);
            L.control.zoom({
                position:'bottomleft'
            }).addTo(map);
            
            var myToken = 'pk.eyJ1IjoiZHlobCIsImEiOiJjaWtubG5uMWYwc3BmdWNqN2hlMzFsNDhvIn0.027tda69GVKbxiPnkEBDAw';
            L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=' + myToken, {
                    maxZoom: 18,
                    attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
                            '<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
                            'Imagery © <a href="http://mapbox.com">Mapbox</a>',
                    id: 'mapbox.outdoors'
            }).addTo(map);
            
            var Mac_unregulated = L.geoJSON(lgas, {
                onEachFeature: function onEach(feature, layer){
                    layer.setStyle({color: 'grey', weight: 1.2, fillOpacity: 0.1});
                }
            }).addTo(map);
            map.setView([-31.8, 148.5], 8);  

            displayed_s10 = [];
            function show_s10(id){
                clear_all_layers();
                document.getElementById('map').style.visibility="visible";
                function getColorScalar(d) {
                    if(d<=Math.floor(max_row/3)){
                        return myCols[2];
                    }else if(d<=Math.ceil(2*max_row/3)){
                        return myCols[1];
                    }else{
                        return myCols[0];
                    }
                }
                function style(feature) {
                    return {
                        weight: 1,
                        opacity: showIt(1),
                        color: 'white',
                        dashArray: '3',
                        fillOpacity: 0.8 * showIt(feature.properties.FUI),
                        fillColor: getColorScalar(feature.properties.IndexRank)
                    };
                }
                var max_row=0;//Get the row number of ranking file
                d3.csv("../pc.csv/potential_for_new_infrastucture_macquaire.csv", function (data) {
                    _.each(data, function (d, i) {
                    max_row++;
                    });
                });

                document.getElementById('s0_title').innerHTML = '<span style="font-size:18px; font-weight:bold; margin-bottom: 0; height: 48px;">'+'Water Source of Macquarie Catchment--Potential need for new infrastructure'+'</span>';
                // control that shows state info on hover
                info = L.control({position: 'topright'});
                info.onAdd = function (map) {
                        this._div = L.DomUtil.create('div', 'info');
                        this.update();
                        return this._div;
                };
                info.update = function (props) {
                    this._div.innerHTML = (props?
                        '<h4>' + props.WATER_SOUR + '</h4>'+
                        'Irrigated Area: '+ '<b>' + toThousands(Math.round(props.irrigated_area*10)/10) + ' Ha' + '</b>' + '<br />'+
//                                            'Population: '+ '<b>' + toThousands(props.population) +'</b>'+'<br />'+
                        'Irrigation Value: '+ '<b>'+ Math.round(toThousands(props.irrigation_value/1000000)*100)/100+' $M' + '</b>'+'<br />'+
//                                            'Mining Value: '+ '<b>' + toThousands(props.mining_value) + ' $M'+'</b>'+'<br />'+
                        'Employment Irrigation: '+ '<b>'+toThousands(props.employment_irrigation) +'</b>'+'<br />'+
//                                            'Employment Mining: '+ '<b>'+ toThousands(props.employment_mining) +'</b>'+'<br />'+
                        'Total Entitlement: '+ '<b>'+ toThousands(props.total_entitlement) + ' ML/year'+ '</b>' +'<br />'+
//                                            'Wetland Area: '+ '<b>'+ toThousands(Math.round(props.wetland_area*10)/10) + ' Ha'+'</b>' +'<br />'+
//                                            'Dissolved Oxygen: '+ '<b>'+ toThousands(props.dissolved_oxygen) + '% mg/L'+ '</b>' +'<br />'+
                        'Mean Flow: '+ '<b>'+ toThousands(Math.round(props.mean_flow*10)/10) + ' ML/day'+'</b>' +'<br />'+
//                                            'Variation: '+ '<b>'+ toThousands(props.variation) + '</b>' +'<br />'+
//                                            'Median: '+ '<b>'+ toThousands(props.median) + ' ML/year'+'</b>' +'<br />'+
//                                            'Days Below Mean: '+ '<b>'+ toThousands(props.days_below_mean) + '</b>' +'<br />'+
                        'DSI: '+ '<b>'+ Math.round(props.DSI/100*100)/100 + '</b>'+'<br />'+
//                                            '100 Years Flood Frequency: '+ '<b>'+ toThousands(props.one_hundred_yrs_flood_frequency) + '</b>'+'<br />'+
//                                            'Time Below Requirement: '+ '<b>'+ toThousands(props.time_below_requirement) + '</b>'+'<br />'+
                        'FUI: '+ '<b>'+ Math.round(props.FUI/100*100)/100 + '</b>'+'<br />'+
//                                            'Water Scarcity: '+ '<b>'+ toThousands(props.water_scarcity) + '</b>'+'<br />'+
                        'Potential for new infrastructure: ' + '<b>'+ Math.round(props.potential_infra*100)/100 + '</b>'+'<br />'
                        : '<b>'+ 'Click a Water Source'+'</b>');
                };
                info.addTo(map);

                var lgaDict = {};
                // initialise each property for of geojson
                for (j = 0; j < lgas.features.length; j++) {
                    lgas.features[j].properties.irrigated_area=0;
                    lgas.features[j].properties.population=0;
                    lgas.features[j].properties.irrigation_value=0;
                    lgas.features[j].properties.mining_value=0;
                    lgas.features[j].properties.employment_irrigation=0;
                    lgas.features[j].properties.employment_mining=0;
                    lgas.features[j].properties.total_entitlement=0;
                    lgas.features[j].properties.agricultural_water_use=0;
                    lgas.features[j].properties.mining_water_use=0;
                    lgas.features[j].properties.wetland_area=0;
                    lgas.features[j].properties.dissolved_oxygen=0;
                    lgas.features[j].properties.mean_flow=0;
                    lgas.features[j].properties.variation=0;
                    lgas.features[j].properties.median=0;
                    lgas.features[j].properties.days_below_mean=0;
                    lgas.features[j].properties.DSI=0;
                    lgas.features[j].properties.one_hundred_yrs_flood_frequency=0;
                    lgas.features[j].properties.time_below_requirement=0;
                    lgas.features[j].properties.FUI=0;
                    lgas.features[j].properties.water_scarcity=0;
                    lgas.features[j].properties.potential_infra=0;
                    lgas.features[j].properties.IndexRank=0;
                    lgaDict[lgas.features[j].properties.WATER_SOUR] = lgas.features[j];
                }

                // Create parallel Coordinate
                parcoords = d3.parcoords()("#parrallel_coordinate")
                .alpha(1)
                .mode("queue") // progressive rendering
                .height(pageHeight-100)
                .width(document.getElementById("parrallel_coordinate").clientWidth - 10)
                .margin({
                        top: 25,
                        left: 1,
                        right: 1,
                        bottom: 15
                })
                .color(function (d) { return getColorScalar(d.IndexRank) });

                //Read data for parallel coordinate
                d3.csv("../pc.csv/potential_for_new_infrastucture_macquaire.csv", function (data) {
                    var keys = Object.keys(data[0]);
                    _.each(data, function (d, i) {
                            d.index = d.index || i; //unique id
                            var water_source_name = d[keys[0]];
                            lgaDict[water_source_name].properties.irrigated_area=d[keys[1]];
                            lgaDict[water_source_name].properties.population=d[keys[2]];
                            lgaDict[water_source_name].properties.irrigation_value=d[keys[3]];
                            lgaDict[water_source_name].properties.mining_value=d[keys[4]];
                            lgaDict[water_source_name].properties.employment_irrigation=d[keys[5]];
                            lgaDict[water_source_name].properties.employment_mining=d[keys[6]];
                            lgaDict[water_source_name].properties.total_entitlement=d[keys[7]];
                            lgaDict[water_source_name].properties.agricultural_water_use=d[keys[8]];
                            lgaDict[water_source_name].properties.mining_water_use=d[keys[9]];
                            lgaDict[water_source_name].properties.wetland_area=d[keys[10]];
                            lgaDict[water_source_name].properties.dissolved_oxygen=d[keys[11]];
                            lgaDict[water_source_name].properties.mean_flow=d[keys[12]];
                            lgaDict[water_source_name].properties.variation=d[keys[13]];
                            lgaDict[water_source_name].properties.median=d[keys[14]];
                            lgaDict[water_source_name].properties.days_below_mean=d[keys[15]];
                            lgaDict[water_source_name].properties.DSI=d[keys[16]];
                            lgaDict[water_source_name].properties.one_hundred_yrs_flood_frequency=parseFloat(d[keys[17]]);
                            lgaDict[water_source_name].properties.time_below_requirement=d[keys[18]];
                            lgaDict[water_source_name].properties.FUI=d[keys[19]];
                            lgaDict[water_source_name].properties.water_scarcity=d[keys[20]];
                            lgaDict[water_source_name].properties.potential_infra=d[keys[21]];
                            lgaDict[water_source_name].properties.IndexRank=d[keys[22]];
                            lga.push(water_source_name);
                    });

                    // add lga layer
                    geojson = L.geoJson(lgas, {
                            style: style,                            
                            onEachFeature: onEachFeature
                    }).addTo(map);

                    // add label layer
                    geojsonLabels = L.geoJson(lgaCentroids, {
                            pointToLayer: function (feature, latlng) {
                                    return  L.marker(latlng, {
                                            clickable : false,
                                            draggable : false,
                                            icon: L.divIcon({
                                            className: 'my-leaflet-div-icon',
                                            })
                                    });
                            },
                    }).addTo(map);
                    displayed_s10.push(geojson);
                    displayed_s10.push(geojsonLabels);                         

                    // add legend
                    legend = L.control({position: 'bottomright'});
                    legend.onAdd = function (map) {
                            var div = L.DomUtil.create('div', 'info legend'),
                            labels = [],
                            from, to;
                            labels.push(
                                            '<i style="background:' + myCols[0] + '"></i> ' +
                                            1 +' (' +'1&ndash;' + Math.floor(max_row/3) + ')');
                            labels.push(
                                            '<i style="background:' + myCols[1] + '"></i> ' +
                                            2 +' (' + (Math.floor(max_row/3)+1) + '&ndash;' + Math.ceil(2*max_row/3) + ')');
                            labels.push(
                                            '<i style="background:' + myCols[2] + '"></i> ' +
                                            3 +' (' + (Math.ceil(2*max_row/3)+1) + '&ndash;' + max_row + ')');
                            div.innerHTML = '<h4>Index Rank</h4>' + labels.join('<br>');
                            return div;
                    };
                    legend.addTo(map);

                    //Bind data to parallel coordinate
                    parcoords.data(data)
                        .hideAxis(["Water source","index"])
                        .render()
                        .reorderable()
                        .brushMode("1D-axes")
                        .rate(400);

                    // setting up grid
                    var column_keys = d3.keys(data[0]);
                    var columns = column_keys.map(function(key,i) {
                        return {
                            id: key,
                            name: key,
                            field: key,
                            sortable: true}
                    });

                    var options = {
                        enableCellNavigation: true,
                        enableColumnReorder: false,
                        multiColumnSort: false,
                    };

                    var dataView = new Slick.Data.DataView();
                    var grid = new Slick.Grid("#grid", dataView, columns, options);

                    grid.autosizeColumns();

                    // wire up model events to drive the grid
                    dataView.onRowCountChanged.subscribe(function (e, args) {
                            grid.updateRowCount();
                            grid.render();
                    });

                    dataView.onRowsChanged.subscribe(function (e, args) {
                            grid.invalidateRows(args.rows);
                            grid.render();
                    });

                    // column sorting
                    var sortcol = column_keys[0];
                    var sortdir = 1;

                    function comparer(a, b) {
                            var x = a[sortcol], y = b[sortcol];
                            return (x == y ? 0 : (x > y ? 1 : -1));
                    }

                    // click header to sort grid column
                    grid.onSort.subscribe(function (e, args) {
                            sortdir = args.sortAsc ? 1 : -1;
                            sortcol = args.sortCol.field;

                            if ($.browser.msie && $.browser.version <= 8) {
                                    dataView.fastSort(sortcol, args.sortAsc);
                            } else {
                                    dataView.sort(comparer, args.sortAsc);
                            }
                    });

                    // highlight row in chart
                    grid.onMouseEnter.subscribe(function(e,args) {
                            var i = grid.getCellFromEvent(e).row;
                            var d = parcoords.brushed() || data;
                            parcoords.highlight([d[i]]);
                    });

                    grid.onMouseLeave.subscribe(function(e,args) {
                            parcoords.unhighlight();
                    });

                    // fill grid with data
                    gridUpdate(data);

                    // update grid on brush
                    parcoords.on("brush", function (d) {
                            filtered = d;
                            isSelected = true;
                            gridUpdate(d);
                            //update map
                            lgas.features.map(function (d) {d.properties.FUI = -1; });
                            geojsonLabels.getLayers().map(function (d) { d._icon.innerHTML = ""; })
                            _.each(d, function (k, i) {
                                    lgaDict[k[keys[0]]].properties.FUI = k.FUI;
                            });

                            map.removeControl(legend);
                            legend.addTo(map);
                            refreshMap(lga);
                    });

                    function gridUpdate(data) {
                            dataView.beginUpdate();
                            dataView.setItems(data);
                            dataView.endUpdate();
                    };

                    function refreshMap(updatedLGA) {
                            // go through updateLGA, or edit the values directly in the geojson layers
                            geojson.getLayers().map(function (d) {
                                    geojson.resetStyle(d);
                                    geojsonLabels.getLayers().forEach(function (z) {
                                            if (z.feature.properties.name == d.feature.properties.WATER_SOUR) {
                                                    if (d.feature.properties.FUI > 0) {
                                                            z._icon.innerHTML=Math.round(d.feature.properties.FUI/100*100)/100;
                                                    } else {
                                                            z._icon.innerHTML = "";
                                                    }
                                            }
                                    });
                            })
                    }
                });
            }
            
            displayed_s11 = [];
            function show_s11(id){
                    clear_all_layers();
                    function getColorScalar(d) {
                        if(d<=Math.floor(max_row/3)){
                        return myCols[2];
                        }else if(d<=Math.ceil(2*max_row/3)){
                        return myCols[1];
                        }else{
                        return myCols[0];
                        }
                    }
                    function style(feature) {
                            return {
                                    weight: 1,
                                    opacity: showIt(1),
                                    color: 'white',
                                    dashArray: '3',
                                    fillOpacity: 0.8 * showIt(feature.properties.FUI),
                                    fillColor: getColorScalar(feature.properties.IndexRank)
                            };
                    }
                    var max_row=0;//Get the row number of ranking file
                    d3.csv("../pc.csv/health_of_water_bodies_population_macquaire.csv", function (data) {
                        _.each(data, function (d, i) {
                        max_row++;
                        });
                    });
                    
                    document.getElementById('s0_title').innerHTML = '<span style="font-size:18px; font-weight:bold; margin-bottom: 0; height: 48px;">'+'Water Source of Macquarie Catchment--Water related health risk due to poor ecosystem health (measured by Dissolved Oxygen level)'+'</span>';
                    grid.style.display = 'block';
                    //control that shows state info on hover
                    info = L.control({position: 'topright'});
                    info.onAdd = function (map) {
                            this._div = L.DomUtil.create('div', 'info');
                            this.update();
                            return this._div;
                    };
                    info.update = function (props) {
                            this._div.innerHTML = (props?
                                    '<h4>' + props.WATER_SOUR + '</h4>'+
//                                            'Irrigated Area: '+ '<b>' + toThousands(Math.round(props.irrigated_area*10)/10) + ' Ha' + '</b>' + '<br />'+
                                            'Population: '+ '<b>' + toThousands(props.population) +'</b>'+'<br />'+
//                                            'Irrigation Value: '+ '<b>'+ Math.round(toThousands(props.irrigation_value/1000000)*100)/100+' $M' + '</b>'+'<br />'+
//                                            'Mining Value: '+ '<b>' + toThousands(props.mining_value) + ' $M'+'</b>'+'<br />'+
//                                            'Employment Irrigation: '+ '<b>'+toThousands(props.employment_irrigation) +'</b>'+'<br />'+
//                                            'Employment Mining: '+ '<b>'+ toThousands(props.employment_mining) +'</b>'+'<br />'+
//                                            'Total Entitlement: '+ '<b>'+ toThousands(props.total_entitlement) + '</b>' +'<br />'+
                                            'Wetland Area: '+ '<b>'+ toThousands(Math.round(props.wetland_area*10)/10) + ' Ha'+'</b>' +'<br />'+
//                                            'Dissolved Oxygen: '+ '<b>'+ toThousands(props.dissolved_oxygen) + '% mg/L' + '</b>' +'<br />'+
                                            'Mean Flow: '+ '<b>'+ toThousands(props.mean_flow) + ' ML/year'+'</b>' +'<br />'+
//                                            'Variation: '+ '<b>'+ toThousands(props.variation) + '</b>' +'<br />'+
//                                            'Median: '+ '<b>'+ toThousands(props.median) + ' ML/year'+'</b>' +'<br />'+
//                                            'Days Below Mean: '+ '<b>'+ toThousands(props.days_below_mean) + '</b>' +'<br />'+
//                                            'DSI: '+ '<b>'+ Math.round(props.DSI/100*100)/100 + '</b>'+'<br />'+
//                                            '100 Years Flood Frequency: '+ '<b>'+ toThousands(props.one_hundred_yrs_flood_frequency) + '</b>'+'<br />'+
//                                            'Time Below Requirement: '+ '<b>'+ toThousands(props.time_below_requirement) + '</b>'+'<br />'+
//                                            'FUI: '+ '<b>'+ Math.round(props.FUI/100*100)/100 + '</b>'+'<br />'+
//                                            'Water Scarcity: '+ '<b>'+ toThousands(props.water_scarcity) + '</b>'+'<br />'+
                                            'Water Related Health Risk Index: ' + '<b>'+ Math.round(props.health_of_water_bodies*1000)/1000 + '</b>'+'<br />'
                                    : '<b>'+ 'Click a Water Source'+'</b>');
                    };
                    info.addTo(map);

                    var lgaDict = {};
//                    var geojson, geojsonLabels;
                    // initialise each property for of geojson
                    for (j = 0; j < lgas.features.length; j++) {
                            lgas.features[j].properties.irrigated_area=0;
                            lgas.features[j].properties.population=0;
                            lgas.features[j].properties.irrigation_value=0;
                            lgas.features[j].properties.mining_value=0;
                            lgas.features[j].properties.employment_irrigation=0;
                            lgas.features[j].properties.employment_mining=0;
                            lgas.features[j].properties.total_entitlement=0;	
                            lgas.features[j].properties.agricultural_water_use=0;
                            lgas.features[j].properties.mining_water_use=0;
                            lgas.features[j].properties.wetland_area=0;
                            lgas.features[j].properties.dissolved_oxygen=0;
                            lgas.features[j].properties.mean_flow=0;
                            lgas.features[j].properties.variation=0;
                            lgas.features[j].properties.median=0;
                            lgas.features[j].properties.days_below_mean=0;
                            lgas.features[j].properties.DSI=0;
                            lgas.features[j].properties.one_hundred_yrs_flood_frequency=0;
                            lgas.features[j].properties.time_below_requirement=0;
                            lgas.features[j].properties.FUI=0;
                            lgas.features[j].properties.water_scarcity=0;
                            lgas.features[j].properties.health_of_water_bodies=0;
                            lgas.features[j].properties.IndexRank=0;
                            lgaDict[lgas.features[j].properties.WATER_SOUR] = lgas.features[j];
                    }

                    // Create parallel Coordinate
                    parcoords = d3.parcoords()("#parrallel_coordinate")
                    .alpha(1)
                    .mode("queue") // progressive rendering
                    .height(pageHeight-100)
                    .width(document.getElementById("parrallel_coordinate").clientWidth)
                    .margin({
                            top: 25,
                            left: 1,
                            right: 1,
                            bottom: 15
                    })
                    .color(function (d) { return getColorScalar(d.IndexRank) });

                    //Read data for parallel coordinate
                    d3.csv("../pc.csv/health_of_water_bodies_population_macquaire.csv", function (data) {
                         var keys = Object.keys(data[0]);
                            _.each(data, function (d, i) {
                                    d.index = d.index || i; //unique id
                                    var water_source_name = d[keys[0]];
                                    lgaDict[water_source_name].properties.irrigated_area=d[keys[1]];
                                    lgaDict[water_source_name].properties.population=d[keys[2]];
                                    lgaDict[water_source_name].properties.irrigation_value=d[keys[3]];
                                    lgaDict[water_source_name].properties.mining_value=d[keys[4]];
                                    lgaDict[water_source_name].properties.employment_irrigation=d[keys[5]];
                                    lgaDict[water_source_name].properties.employment_mining=d[keys[6]];
                                    lgaDict[water_source_name].properties.total_entitlement=d[keys[7]];
                                    lgaDict[water_source_name].properties.agricultural_water_use=d[keys[8]];
                                    lgaDict[water_source_name].properties.mining_water_use=d[keys[9]];
                                    lgaDict[water_source_name].properties.wetland_area=d[keys[10]];
                                    lgaDict[water_source_name].properties.dissolved_oxygen=d[keys[11]];
                                    lgaDict[water_source_name].properties.mean_flow=d[keys[12]];
                                    lgaDict[water_source_name].properties.variation=d[keys[13]];
                                    lgaDict[water_source_name].properties.median=d[keys[14]];
                                    lgaDict[water_source_name].properties.days_below_mean=d[keys[15]];
                                    lgaDict[water_source_name].properties.DSI=d[keys[16]];
                                    lgaDict[water_source_name].properties.one_hundred_yrs_flood_frequency=parseFloat(d[keys[17]]);
                                    lgaDict[water_source_name].properties.time_below_requirement=d[keys[18]];
                                    lgaDict[water_source_name].properties.FUI=d[keys[19]];
                                    lgaDict[water_source_name].properties.water_scarcity=d[keys[20]];
                                    lgaDict[water_source_name].properties.health_of_water_bodies=d[keys[21]];
                                    lgaDict[water_source_name].properties.IndexRank=d[keys[22]];
                                    lga.push(water_source_name);
                            });

                            // add lga layer
                            geojson = L.geoJson(lgas, {
                                    style: style,                            
                                    onEachFeature: onEachFeature
                            }).addTo(map);

                            // add label layer
                            geojsonLabels = L.geoJson(lgaCentroids, {
                                    pointToLayer: function (feature, latlng) {
                                            return  L.marker(latlng, {
                                                    clickable : false,
                                                    draggable : false,
                                                    icon: L.divIcon({
                                                    className: 'my-leaflet-div-icon',
                                                    })
                                            });
                                    },
                            }).addTo(map);
                            displayed_s11.push(geojson);
                            displayed_s11.push(geojsonLabels);                         

                            // add legend
                            legend = L.control({position: 'bottomright'});
                            legend.onAdd = function (map) {
                                    var div = L.DomUtil.create('div', 'info legend'),
                                    labels = [],
                                    from, to;
                                    labels.push(
                                                    '<i style="background:' + myCols[0] + '"></i> ' +
                                                    1 +' (' +'1&ndash;' + Math.floor(max_row/3) + ')');
                                    labels.push(
                                                    '<i style="background:' + myCols[1] + '"></i> ' +
                                                    2 +' (' + (Math.floor(max_row/3)+1) + '&ndash;' + Math.ceil(2*max_row/3) + ')');
                                    labels.push(
                                                    '<i style="background:' + myCols[2] + '"></i> ' +
                                                    3 +' (' + (Math.ceil(2*max_row/3)+1) + '&ndash;' + max_row + ')');
                                    div.innerHTML = '<h4>Index Rank</h4>' + labels.join('<br>');
                                    return div;
                            };
                            legend.addTo(map);

                            //Bind data to parallel coordinate
                            parcoords.data(data)
                                .hideAxis(["Water source","index"])
                                .render()
                                .reorderable()
                                .brushMode("1D-axes")
                                .rate(400);

                            // setting up grid
                            var column_keys = d3.keys(data[0]);
                            var columns = column_keys.map(function(key,i) {
                                    return {
                                            id: key,
                                            name: key,
                                            field: key,
                                            sortable: true}
                            });

                            var options = {
                                    enableCellNavigation: true,
                                    enableColumnReorder: false,
                                    multiColumnSort: false,
                            };

                            var dataView = new Slick.Data.DataView();
                            var grid = new Slick.Grid("#grid", dataView, columns, options);
                            grid.autosizeColumns();

                            // wire up model events to drive the grid
                            dataView.onRowCountChanged.subscribe(function (e, args) {
                                    grid.updateRowCount();
                                    grid.render();
                            });

                            dataView.onRowsChanged.subscribe(function (e, args) {
                                    grid.invalidateRows(args.rows);
                                    grid.render();
                            });

                            // column sorting
                            var sortcol = column_keys[0];
                            var sortdir = 1;

                            function comparer(a, b) {
                                    var x = a[sortcol], y = b[sortcol];
                                    return (x == y ? 0 : (x > y ? 1 : -1));
                            }

                            // click header to sort grid column
                            grid.onSort.subscribe(function (e, args) {
                                    sortdir = args.sortAsc ? 1 : -1;
                                    sortcol = args.sortCol.field;
                                    if ($.browser.msie && $.browser.version <= 8) {
                                            dataView.fastSort(sortcol, args.sortAsc);
                                    } else {
                                            dataView.sort(comparer, args.sortAsc);
                                    }
                            });

                            // highlight row in chart
                            grid.onMouseEnter.subscribe(function(e,args) {
                                    var i = grid.getCellFromEvent(e).row;
                                    var d = parcoords.brushed() || data;
                                    parcoords.highlight([d[i]]);
                            });

                            grid.onMouseLeave.subscribe(function(e,args) {
                                    parcoords.unhighlight();
                            });

                            // fill grid with data
                            gridUpdate(data);

                            // update grid on brush
                            parcoords.on("brush", function (d) {
                                    filtered = d;
                                    isSelected = true;
                                    gridUpdate(d);
                                    //update map
                                    lgas.features.map(function (d) {d.properties.FUI = -1; });
                                    geojsonLabels.getLayers().map(function (d) { d._icon.innerHTML = ""; })
                                    _.each(d, function (k, i) {
                                            lgaDict[k[keys[0]]].properties.FUI = k.FUI;
                                    });

                                    map.removeControl(legend);
                                    legend.addTo(map);
                                    refreshMap(lga);
                            });


                            function gridUpdate(data) {
                                    dataView.beginUpdate();
                                    dataView.setItems(data);
                                    dataView.endUpdate();
                            };

                            function refreshMap(updatedLGA) {
                                    // go through updateLGA, or edit the values directly in the geojson layers
                                    geojson.getLayers().map(function (d) {
                                            geojson.resetStyle(d);
                                            geojsonLabels.getLayers().forEach(function (z) {
                                                    if (z.feature.properties.name == d.feature.properties.WATER_SOUR) {
                                                            if (d.feature.properties.FUI > 0) {
                                                                    z._icon.innerHTML=Math.round(d.feature.properties.FUI/100*100)/100;
                                                            } else {
                                                                    z._icon.innerHTML = "";
                                                            }
                                                    }
                                            });
                                    })
                            }
                    });
            }
        </script>
    </body>
</html>