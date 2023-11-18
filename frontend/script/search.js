const results_per_page = 100;

const url = new URL(location.href).searchParams;

const page = parseInt(url.get("page") || "0");
const query = url.get("q");

document.getElementById("search").value = query;
document.getElementById("title").innerText = "Search - " + query;
document.getElementById("page").innerText = page;

const start_time = new Date().getTime();

fetch("/api/search?q=" + query + "&offset=" + results_per_page * page + "&limit=" + results_per_page).then(res => res.json().then(res => {
	if (res.error) {
		console.log(res);
		alert("Fetch failed! " + res.error);
		return;
	}




	const resultCount = document.createElement("h4");
	resultCount.innerText = res.length + " search results";
	// warum wird das erst am Ende angefügt?
	document.getElementById("results").appendChild(resultCount);

	const result_div = document.getElementById("Resultinp");

	for (const i of res) {
		const contDiv = document.createElement("div");
		contDiv.setAttribute("class", "resultContainer");
		const uppDiv = document.createElement("div");
		uppDiv.setAttribute("class", "upper");

		const favi = document.createElement("img");

		const infoDiv = document.createElement("div");
		infoDiv.setAttribute("class", "info");

		const br = document.createElement("br");
		
		const link = document.createElement("small");
		link.setAttribute("id", "link");
		link.innerText = i.link;

		const bottomDiv = document.createElement("div");
		bottomDiv.setAttribute("class", "bottom");

		const infoText = document.createElement("h5");
		// "info input from website"
		infoText.setAttribute("id", "info");

		// Wenn vorhanden || gewollt
		const sideImg = document.createElement("img");
		sideImg.setAttribute("class", "img");


		const title = document.createElement("a");
		title.setAttribute("id", "title");
		title.setAttribute("class", "Title");
		title.setAttribute("href", i.link);
		title.innerText = i.title;

		contDiv.appendChild(uppDiv);
		uppDiv.appendChild(favi);
		uppDiv.appendChild(infoDiv);
		infoDiv.appendChild(link);
		infoDiv.appendChild(br);
		infoDiv.appendChild(title);
		contDiv.appendChild(bottomDiv);
		bottomDiv.appendChild(infoText);
		bottomDiv.appendChild(sideImg);

		result_div.appendChild(contDiv);
	}


	const time = new Date().getTime() - start_time;
	document.getElementById("results_time").innerText = "Search took " + (time / 1000) + "s";
}));

document.getElementById("prev").addEventListener('click', event => {
	location.href = "/search.html?q=" + query + "&page=" + (page - 1);
});

document.getElementById("next").addEventListener('click', event => {
	location.href = "/search.html?q=" + query + "&page=" + (page + 1);
});


if (page == 0) {
	document.getElementById("prev").disabled = true;
}

