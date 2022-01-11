<template>
  <div class="toolbar-create">
    <div>
      <button @click="change_status(true)">作成</button>
    </div>
  </div>
</template>

<script>
export default {
  methods: {
    change_status(stat) {
      this.$store.commit("createfoldermodal_mutation", {
        status: stat,
      });
    },
  },
};
</script>

<style scoped>
.toolbar-create {
  margin: 0 4px;
}
</style>
