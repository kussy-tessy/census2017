<template>
  <div v-if="!isGroup">
    <select v-model="selectedComputed">
      <option v-for="option in options" v-bind:value="option.id" :key="option.id">
        {{ option.desc }}
      </option>
    </select>
  </div>
  <div v-else>
    <select v-model="selectedComputed">
      <optgroup v-for="_options in options" :label="_options.desc">
        <option v-for="option in _options[children]" :value="option.id" :key="option.id">
          {{ option.desc }}
        </option>
      </optgroup>
    </select>
  </div>
  </select>
</template>

<script>
  export default {
    props: ['selected', 'options', 'children'],
    /*data: function(){
      return {
        mySelected: this.value.selected,
        options: this.value.options
      }
    },
    methods: {
      change: function(){
        console.log(this.mySelected);
        this.$emit('change', this.mySelected);
      }
    },*/
    computed: {
      isGroup: function(){
        return true;
        // return !this.options[0] === undefined;
      },
      selectedComputed: {
        get: function(){
          return this.selected;
        },
        set: function(newVal){
          this.$emit('update:selected', newVal);
          this.$emit('change');
        }
      }
    }
  }
</script>