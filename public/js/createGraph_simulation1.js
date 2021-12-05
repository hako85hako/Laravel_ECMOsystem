/**
 * 圧力損失のグラフを作成する
 */
	const aryMax = function (a, b) {return Math.max(a, b);}
	let max_log = 0;
	let flow_list_log = []
	let array_data = [];
	//for(let i=0;i<speed_list.length;i++){
		colorCode = selectColor(5);
		single_data = {
			type: 'line',
			label: '物品通過後の圧力',
			data: graphData,
			pointHoverRadius: 8,
			//backgroundColor: "rgba(54, 162, 235, 0.2)",
			borderColor: colorCode,
			lineTension: 0,
			borderWidth: 1,
			//fill: 'origin',
		};
			array_data.push(single_data);
			//testflow_list = flow_list[speed_list[i]]
			//let max = testflow_list.reduce((acc, value) => (acc > value ? acc : value))
			//let max = testflow_list.reduce(aryMax);
			//if(max>max_log){
			//	max_log = max;
			//	flow_list_log = testflow_list;
			//}
	//}
	data ={ labels: graphLabel,
			datasets:array_data
		};


    var ctx = document.getElementById('simulation1Graph');
	new Chart(ctx, {
	    type: 'line',
	    data: data,
	    options: {
        	responsive: true,
        	maintainAspectRatio: false,
			title:{
				display: true,
				text: '圧力推移[mmHg]'
			},
		      plugins: {
		        filler: {
		          //propagate: true
	        }
	      },
		scales:{
			yAxes: [{
				ticks: {
					suggestedMax: 300,
					suggestedMin: 0,
					stepSize: 50,
					}
				}]
			}
	    }
	});

	function selectColor(colorNum){
		 switch (colorNum%6) {
			case 0:
			    colorCode = "#36A2EB";
			    break;
			case 1:
			    colorCode = "#F08681";
			    break;
			case 2:
				colorCode = "#F0C78D";
			    break;
			case 3:
				colorCode = "#B375F0";
			    break;
			case 4:
				colorCode = "#5DF092";
			    break;
			case 5:
				colorCode = "#FFC5D3";
			    break;
		}
		return colorCode;
	}
