<!DOCTYPE html>
<meta charset="utf-8">
<style>

body {
	background: #fff;
	font-family: 'Pathway Gothic One', sans-serif;
}

svg {margin: 0 auto;}

.land {
  	fill: #dfdfdf;
  	stroke: #fff;
    z-index: 1;
}

.boundary {
  fill: #dfdfdf;
  stroke: #fff;
  stroke-width: .5px;
}

.symbol {
  fill: #66c697;
  fill-opacity: .7;
  stroke: #008a46;
    stroke-width: .8px;
  z-index: 2;
}

#infos-background {
	position: absolute;
/* 
	background-color: rgba(255,255,255,.8);
 */
	opacity: .8;
	left: 730px;
	width: 230px;
	height:500px;
}

.infos {
	position: relative;
	float:right;
	font-size: x-large;
}

#country_name {position: absolute;margin-right: 30px;margin-top: 100px;}
#country_tor {position: absolute;margin-top: 150px;}
#date {position: absolute;margin-top: 432px;}

#timeline-background {
	position: absolute;
	background-color: #fff;
	opacity: .8;
	top: 430px;
	width: 960px;
	height:70px;
}

#timeline {
	width: 500px;
	top: 20px;
	left: 150px;
}

</style>
<body>
<link type="text/css" href="js/jquery-ui-1.10.3.custom/css/smoothness/jquery-ui-1.10.3.custom.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js"></script>
<link href='http://fonts.googleapis.com/css?family=Pathway+Gothic+One' rel='stylesheet' type='text/css'>
<script src="http://d3js.org/d3.v3.min.js"></script>
<script src="http://d3js.org/topojson.v1.min.js"></script>
<script src="http://d3js.org/d3.geo.projection.v0.min.js" charset="utf-8"></script>
<script src="http://d3js.org/queue.v1.min.js"></script>
<script>

$(function() {

$("#timeline").slider({ 
	animate: 200,
	range:"min",
	min: 1,
	max: 61,
	step: 1,
	value:61,
	slide: function(event,ui){
		year_i = ui.value;
		update(year_i);
		maj_date(year_i);
		$("#country_name").hide();
		$("#country_tor").hide();
	}
});

var dates = ["May 2008","June 2008","July 2008","August 2008","September 2008","October 2008","November 2008","December 2008","January 2009","February 2009","March 2009","April 2009","May 2009","June 2009","July 2009","August 2009","September 2009","October 2009","November 2009","December 2009","January 2010","February 2010","March 2010","April 2010","May 2010","June 2010","July 2010","August 2010","September 2010","October 2010","November 2010","December 2010","January 2011","February 2011","March 2011","April 2011","May 2011","June 2011","July 2011","August 2011","September 2011","October 2011","November 2011","December 2011","January 2012","February 2012","March 2012","April 2012","May 2012","June 2012","July 2012","August 2012","September 2012","October 2012","November 2012","December 2012","January 2013","February 2013","March 2013","April 2013","May 2013","June 2013"];
var year_i = 61;
var current_country;

var width = 960,
    height = 500;

var radius = d3.scale.sqrt()
    .domain([0, 90000])
    .range([0, 30]);
    
var projection = d3.geo.mollweide()
    .scale((width + 1) / 2 / Math.PI)
    .translate([width / 2 - 140, height / 2])
    .precision(.1);

var path = d3.geo.path()
    .projection(projection);

var svg = d3.select("body").append("svg")
    .attr("width", width)
    .attr("height", height);

var infos = d3.select("#infos-background");
	
infos.append("span")
	.attr("id", "country_name")
	.attr("class","infos");
	
infos.append("span")
	.attr("id", "country_tor")
	.attr("class","infos");

infos.append("span")
	.attr("id", "date")
	.attr("class","infos")
	.text("June 2013");

queue()
    .defer(d3.json, "data/countries2010.json")
    .defer(d3.json, "data/tor2010.json")
    .await(ready);

function ready(error, world, centroid) {

  svg.insert("path", "svg")
      .datum(topojson.feature(world, world.objects.pays, function(a, b) { return a !== b; }))
      .attr("class", "boundary")
      .attr("d", path);

svg.selectAll(".symbol")
      .data(centroid.features.sort(function(a, b) { return b.tor_data.by_year[1,61] - a.tor_data.by_year[1,61]; }))
    .enter().append("path")
      .attr("class", "symbol")
      .attr("d", path.pointRadius(function(d) { return radius(d.tor_data.by_year[1,61]);}))
      .on("mouseover", function(d){
      		current_country = d.properties.name;
      		$("#country_name").show();
      		$("#country_tor").show();
			$("#country_name").text(current_country);
			$("#country_tor").text(d.tor_data.by_year[1,year_i]+" TOR users");})
      
svg.append("svg:text").attr("x",190).attr("y",470).text("2009");
svg.append("svg:text").attr("x",290).attr("y",470).text("2010");
svg.append("svg:text").attr("x",390).attr("y",470).text("2011");
svg.append("svg:text").attr("x",490).attr("y",470).text("2012");
svg.append("svg:text").attr("x",590).attr("y",470).text("2013");

};

function update(year) {
	d3.json("data/tor2010.json", function(json){
		svg.selectAll(".symbol")
      .data(json.features.sort(function(a, b) { return b.tor_data.by_year[1,61] - a.tor_data.by_year[1,61]; }))
			.transition().ease("linear").duration(300)
				.attr("d", path.pointRadius(function(d) { return radius(d.tor_data.by_year[1,year]);}))
	})
};

function maj_date(year){
	$("#date").text(dates[year]);
}

function maj_tordata(year){
	$("#date").text(json("data/tor2010.json"[current_country][year]));
	$("#country_tor").text(json("data/tor2010.json"[current_country][year]));	
}

});
</script>
	<div id="timeline-background">
		<div id="timeline"></div>
		<div id="timeline-dates"></div>
	</div>
	<span id="infos-background">
	</span>
</body>

