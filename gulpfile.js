elixir(function(mix) {
  var bootstrapPath = 'node_modules/bootstrap-sass/assets';
  mix.sass('app.scss')
    .copy(bootstrapPath + '/fonts', 'public/fonts')
    .copy(bootstrapPath + '/javascripts/bootstrap.min.js', 'public/js');
});