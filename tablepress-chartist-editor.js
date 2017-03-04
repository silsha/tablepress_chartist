(function() {
	tinymce.PluginManager.add('tablepress_chartist_button', function( editor, url ) {
		editor.addButton( 'tablepress_chartist_button', {
			text: 'TablePress Chartist',
			icon: false,
			onclick: function() {
				editor.windowManager.open( {
					title: 'Insert TablePress Chart',
					body: [{
						type: 'textbox',
						name: 'id',
						label: 'TablePress ID'
					},
					{
						type: 'listbox',
						name: 'chart',
						label: 'Chart type',
						'values': [
						{text: 'Select type …', value: false},
						{text: 'Bar', value: 'bar'},
						{text: 'Line', value: 'line'},
						{text: 'Pie', value: 'pie'},
						{text: 'Donut', value: 'donut'},
						{text: 'Percent', value: 'percent'}
						]
					},
					{
						type: 'listbox',
						name: 'showarea',
						label: 'Show Chart Area',
						'values': [
						{text: 'Select …', value: false},
						{text: 'Yes', value: 'true'},
						{text: 'No', value: 'false'}
						]
					},
					{
						type: 'listbox',
						name: 'linesmooth',
						label: 'Smooth Line',
						'values': [
						{text: 'Select …', value: false},
						{text: 'Yes', value: 'true'},
						{text: 'No', value: 'false'}
						]
					},
					{
						type: 'listbox',
						name: 'showpoint',
						label: 'Show Line Points',
						'values': [
						{text: 'Select …', value: false},
						{text: 'Yes', value: 'true'},
						{text: 'No', value: 'false'}
						]
					},
					{
						type: 'listbox',
						name: 'horizontal',
						label: 'Horizontal Bars',
						'values': [
						{text: 'Select …', value: false},
						{text: 'Yes', value: 'true'},
						{text: 'No', value: 'false'}
						]
					},
					{
						type: 'listbox',
						name: 'stack',
						label: 'Stacked Bars',
						'values': [
						{text: 'Select …', value: false},
						{text: 'Yes', value: 'true'},
						{text: 'No', value: 'false'}
						]
					},
					{
						type: 'listbox',
						name: 'animation',
						label: 'Animation',
						'values': [
						{text: 'No animation', value: false},
						{text: 'Buildup', value: 'buildup'},
						]
					},
					{
						type: 'listbox',
						name: 'aspect_ratio',
						label: 'Aspect Ratio',
						'values': [
						{text: 'Select …', value: false},
						{text: '1', value: '1'},
						{text: '15:16', value: '15:16'},
						{text: '8:9', value: '8:9'},
						{text: '5:6', value: '5:6'},
						{text: '4:5', value: '4:5'},
						{text: '3:4', value: '3:4'},
						{text: '2:3', value: '2:3'},
						{text: '5:8', value: '5:8'},
						{text: '1:1.618', value: '1:1.618'},
						{text: '3:5', value: '3:5'},
						{text: '9:16', value: '9:16'},
						{text: '8:15', value: '8:15'},
						{text: '1:2', value: '1:2'},
						{text: '2:5', value: '2:5'},
						{text: '3:8', value: '3:8'},
						{text: '1:3', value: '1:3'},
						{text: '1:4', value: '1:4'}
						]
					},
					{
						type: 'textbox',
						name: 'donut_width',
						label: 'Donut Linie Width'
					},
					{
						type: 'textbox',
						name: 'label_offset',
						label: 'Label Offset'
					},
					{
						type: 'textbox',
						name: 'chart_padding',
						label: 'Chart Padding'
					}],
					onsubmit: function( e ) {
						var content = "[table-chart ";

						if (!e.data.id.length) {
							tinymce.activeEditor.notificationManager.open({
								text: 'Please enter a TablePress ID.',
								type: 'error'
							});
							e.preventDefault();
							return;
						}

						for (var key in e.data) {
							if (e.data.hasOwnProperty(key) && e.data[key]) {
								content = content + key + "=" + e.data[key] + " ";
							}
						}

						content = content + "]";

						editor.insertContent( content );
					}
				});
			}
		});
	});
})();
