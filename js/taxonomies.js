Vue.config.productionTip = false
Vue.config.devtools = false

const app = new Vue({
  el: '.taxonomies',
  data: {
    lookup: {}
  },
  async mounted() {
    const d = await fetch(window.restUrl + 'hsn-theme/bijdrage-taxonomies').then(r => r.json())
    if (!d || !d.forEach) {
      return console.warn('API error', d)
    }
    d.forEach(tax => {
      tax.open = false
      tax.terms.forEach(term => {
        term.open = false
        term.selected = false
        if (term.terms && term.terms.length) {
          term.terms.forEach(sub => {
            sub.selected = false
          })
          term.subs = term.terms.length
        }
      })
    })
    console.log('d', d)
    this.lookup = d
  }
})
