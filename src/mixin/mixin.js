export default {
  methods: {
    get_path() {
      return decodeURI(window.location.pathname);
    },
  },
};
