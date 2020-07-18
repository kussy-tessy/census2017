<template>
  <div>
    <div @change="changeType">
      <label><input type="radio" v-model="viewType" value="single">単質問</label>
      <label><input type="radio" v-model="viewType" value="corr">相関関係</label>
    </div>
    <div @change="changeLimitation">
      <div>
        <label><input type="checkbox" v-model="onlyWorker" value="true">社会人に限定する</label><br>
        <label><input type="checkbox" v-model="onlyActive" value="true">活動中の人に限定する</label><br>
        <label><input type="checkbox" v-model="onlyActor" value="true">内臓に限定する</label>
      </div>
    </div>
    <select-box v-bind.sync="first" @change="change"></select-box>
    <select-box v-if="viewType==='corr'" v-bind.sync="second" @change="change"></select-box>
  </div>
</template>

<script>
  import SelectBox from './SelectBox.vue'

  export default {
    components: {
      'select-box': SelectBox
    },
    data: function(){
      return {
        viewType: 'single',
        first: {
          selected: '',
          options: []
        },
        second: {
          selected: '',
          options: [] 
        },
        onlyWorker: false,
        onlyActive: false,
        onlyActor: false,
      };
    },
    mounted: function() {
      this.changeType();
    },
    methods: {
      changeType: function(){
        axios.get('/question', {
          params: {
            isSingle: this.viewType === 'single'
          }
        }).then(res => {
          this.first.options = res.data.questionGroups;
          this.second.options = res.data.questionGroups;
          const childrenAttr = 'questions'
          this.first.children =  this.second.children = childrenAttr;
          this.first.selected = this.first.options[0][childrenAttr][0]['id'];
          this.second.selected = this.first.options[0][childrenAttr][0]['id'];
          this.change();
        });
      },
      changeLimitation: function(){
        axios.get('/question', {
        params: {
          isSingle: this.viewType === 'single'
          }
        }).then(res => {
          this.change();
        });
      },
      change: function(){
        axios.get('/answer', {
          params: {
            first: this.first.selected,
            second: this.viewType === 'corr' ? this.second.selected : null,
            onlyWorker: this.onlyWorker,
            onlyActive: this.onlyActive,
            onlyActor: this.onlyActor
          }
        }).then(res => {
          this.$emit('changeGraph', {data: res.data, isSingle: this.viewType === 'single'});
        });
      },
    }
  }
</script>