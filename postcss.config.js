module.exports = {
  plugins: [
    require('postcss-import'),      // pour gérer les @import
    require('postcss-nesting'),     // CSS imbriqué
    require('autoprefixer'),        // ajout des prefixes
    require('cssnano')({ preset: 'default' })
  ].filter(Boolean)
};