document.addEventListener("DOMContentLoaded", () => {
  setTimeout(function () {
    var now = new Date().toJSON().slice(0, 10).replace(/-/g, '/');
    var inputs = document.querySelectorAll('input.js-honey');
    inputs.forEach((input) => {
      input.value = 'abcd1234-' + now;
    });
  }, 5000);
});
