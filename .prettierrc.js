module.exports = {
  printWidth: 100,
  tabWidth: 2,
  singleQuote: true,
  semi: false,
  trailingComma: "es5", // [],
  bracketSpacing: true, // { foo: bar }
  arrowParens: "always", // () => {}
  useTabs: false,
  overrides: [
    {
      files: "*.php",
      options: {
        tabWidth: 4,
        semi: false
      }
    }
  ]
}
