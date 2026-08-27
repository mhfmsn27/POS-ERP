mapboxgl.accessToken =
  "pk.eyJ1IjoidGhlbWVzZmxhdCIsImEiOiJjbGt3NGxtYncwa2F2M21saHM3M21uM3h2In0.9NbzjykXil1nELxQ1V8rkA";
const map = new mapboxgl.Map({
  container: "map-track", // container ID
  style: "mapbox://styles/mapbox/streets-v12", // style URL
  center: [-75.690878, 45.417984], // starting position [lng, lat]
  zoom: 15 
});

document.addEventListener("DOMContentLoaded", () => map.resize());

$(window).trigger('resize');