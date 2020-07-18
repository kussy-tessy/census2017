<template>
  <div>
    <question-selector @changeGraph="changeGraph"></question-selector>
    <div>
      <chart-item :myData="myData"></chart-item>
    </div>
  </div>
</template>

<script>
  import QuestionSelector from './QuestionSelector.vue'
  import Chart from './Chart.vue'

  export default {
    components: {
      'question-selector': QuestionSelector,
      'chart-item': Chart
    },
    data: function(){
      return {
        myData: {},
      };
    },
    methods: {
      changeGraph: function(params){
        this.myData = params.data;
      }
    },
  }
</script>