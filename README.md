#TablePress Extension: Chartist

![Sample chart](wp-assets/screenshot-1.jpg)

##About

Using [Chartist.js](http://gionkunz.github.io/chartist-js/), this [TablePress](https://tablepress.org/) Extension creates a responsive chart based on the data in a TablePress table.

##Usage

Add the Shortcode `[table-chart id=123 /]` to a post or page to create a chart from the TablePress table 123.

**Optional parameters:**
* Show/hide chart line: `showline=true` (default: true)
* Show/hide show chart area: `showarea=false` (default: false)
* Set chart y low: `low=0` (default: table low)
* Set chart y high: `high=10` (default: table high)
* Set line with of the donut chart: `donut_width=200` (default: false)
* Enable/disable smooth line: `linesmooth=true` (default: true)
* Enable/disable line points: `showpoint=true` (default: true)
* Enable/disable horizontal bars: `horizontal=true` (default: false)
* Enable/disable stacked bars: `stack=true` (default: false)
* Set chart aspect ratio: `aspect_ratio=3:4` (default: 3:4) Alternatives: 1, 15:16, 8:9, 5:6, 4:5, 3:4, 2:3, 5:8, 1:1.618, 3:5, 9:16, 8:15, 1:2, 2:5, 3:8, 1:3, or 1:4
* Set label offset: `label_offset=100` (default: false)
* Set chart padding: `chart_padding=100` (default: false)
* Select chart type: `chart=bar` (default: line) Alternatives: line, bar, pie, donut, percent or piepercent (mix of pie and percent).
* Use animations (not available for all chart types): `animation=buildup` (default: false)

If the "Table Head Row" option is enabled for the table, the Extension will use the head row data for the chart labels.
The other rows will be shown as lines or bars. Pie or percent charts will only use the first data row. Percent charts will ignore the header row.

##CSS customizations

If you'd like to overide [the default style](http://gionkunz.github.io/chartist-js/getting-started.html#the-sass-way), you can add a `tablepress-chartist-custom.css` in `wp-content` directory. It will be loaded after the Extension's default CSS file `libdist/chartist.min.css`.

**Example:**
```css
/**
 * SVG Shape CSS properties: http://tutorials.jenkov.com/svg/svg-and-css.html#shape-css-properties
 */

/* First line / bar is .ct-series-a, next is .ct-series-b etc. */
.ct-chart .ct-series.ct-series-a .ct-bar,
.ct-chart .ct-series.ct-series-a .ct-line,
.ct-chart .ct-series.ct-series-a .ct-point  {
	stroke: #073DA0;
}

.ct-series .ct-line, .ct-chart .ct-bar {
	fill: none;
	stroke-width: 10px;
}

.ct-chart .ct-point {
	stroke-width: 10px;
	stroke-linecap: round;
}
```

##Installation

**Prerequisite:** The [TablePress](https://tablepress.org/) plugin

1. In `Plugins->Add New`, search for `tablepress chartist`
1. Click `Install Now`
1. When the plugin is installed, activate it.

##Changelog
* 0.9 Updated chartist.js to 0.11.0
* 0.8 Added support for pie charts with percent labels, added animation for bar charts. Added parameters `label_offset`, `chart_padding` and `donut_width`.
* 0.7 Added support for horizontal bars, stacked bars and donut charts.
* 0.6 Switch to `[table-chart]` Shortcode.
* 0.5.1 Revert to PHP json_encode().
* 0.5 **Breaking change**: Simplified optional parameters (removed prefix `chartist_`), new optional parameters are: showline, showarea, low, high, linesmooth, showpoint and aspect_ratio. See examples in [screenshots](https://wordpress.org/plugins/tablepress-chartist/screenshots/). Added support for `chart=pie` and `chart=percent`.
* 0.4 Added support for bar chart: `chart=bar`.
* 0.3 Added support for CSS customizations.
* 0.2 Added more optional parameters.
* 0.1 Initial release (i.e. an early beta).

##Credits

* Gion Kunz for creating [Chartist.js](http://gionkunz.github.io/chartist-js/)
* Tobias Bäthge for creating [TablePress](https://tablepress.org/)

##Copyright and License

TablePress Extension: Chartist is copyright 2014 Per Soderlind

TablePress Extension: Chartist is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or (at your option) any later version.

TablePress Extension: Chartist is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the [GNU General Public License](LICENSE) for more details.

You should have received a copy of the GNU Lesser General Public License along with the Extension. If not, see http://www.gnu.org/licenses/.
