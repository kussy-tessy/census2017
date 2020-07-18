<template>
  <div style="width:70%; min-width: 300px; max-width: 500px;">
    <bar-chart v-if="dispChart === 'bar'" :chart-data="barDatasets" :height="400" :width="500" :options="options"></bar-chart>
    <line-chart v-if="dispChart === 'line'" :chart-data="lineDatasets" :height="400" :width="500" :options="options"></line-chart>
    <div v-if="!dataIsSingle">
      <button v-if="dataIsQuant" @click="changeGraph">グラフ切り替え</button>
      <label v-if="dispChart === 'bar'"><input type="checkbox" v-model="is100percent">100%積み上げ</label>
    </div>
  </div>
</template>

<script>
  import Bar from './base/Bar.js'
  import Line from './base/Line.js'

  export default {
    components: {
      'bar-chart': Bar,
      'line-chart': Line
    },
    props: ['myData'],
    data () {
      return {
        color: ['#FF7F7F', '#FFBF7F', '#FFFF7F', '#7FFF7F', '#7F7FFF', '#BF7FFF', '#FF7F7F', '#FFBF7F', '#FFFF7F', '#7FFF7F', '#7F7FFF'],
        chart: 'bar',
        is100percent: false,
      }
    },
    methods: {
      changeGraph (){
        this.chart = this.chart == 'line' ? 'bar' : 'line';
      }
    },
    computed: {
      dataIsSingle(){
        if(this.myData.ylabel === undefined) return true;
        return this.myData.ylabel.length === 1;
      },
      dataIsQuant(){
        return this.myData.isQuant;
      },
      dispChart(){
        if(!this.dataIsSingle && this.chart == 'line' && this.dataIsQuant){
          return 'line';
        } else {
          return 'bar';
        }
      },
      disp100percent(){
        return !this.dataIsSingle && this.chart == 'bar' && this.is100percent;
      },
      ratios: function(){
        const sums = this.myData.value.map(va => va.reduce((a, b) => a + b, 0));
        return this.myData.value.map((va, i) => va.map(v => Math.round(v / sums[i] * 100)));
      },
      barDatasets: function() {
        const that = this;
        if(that.myData.xlabel === undefined) return {};
        const data = that.disp100percent ? that.ratios : that.myData.value;
        return {
          labels: that.myData.xlabel,
          datasets: [...Array(that.myData.ylabel.length)].map((_, idx) => new Object({
            label: that.myData.ylabel[idx],
            backgroundColor: that.color[idx],
            data: data.map(va => va[idx]),
          }))
        };
      },
      lineDatasets: function() {
        const that = this;
        return {
          labels: that.myData.xlabel,
          datasets: [{
            data: that.ratios.map(ratio => ratio.map((ra, idx) => ra * that.myData.repr[idx]/100)).map(ratio => ratio.reduce((x,y) => x + y, 0 )),
            borderColor: '#FF7F7F',
            pointBackgroundColor: '#FF7F7F',
            backgroundColor: "rgba(0,0,0,0)",
            lineTension: 0,
          }]
        }
      },
      options: function(){
        let options = {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            xAxes: [{stacked: true, offset: true, ticks:{autoSkip: false}}],
            yAxes: [{stacked: true}]
          },
          spanGaps: true,
          legend: {
            position: 'bottom'
          }
        }
        if (this.dataIsSingle){
          options.legend['display'] = false;
        }
        if(this.dispChart){
          options.legend['display'] = false;
        }
        if (this.disp100percent) {
          options.scales.yAxes[0]['ticks'] = {max: 100};
        }
        return options;
      },
    }
  }
</script>