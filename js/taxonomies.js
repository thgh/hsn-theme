Vue.config.productionTip = false
Vue.config.devtools = false

const app = new Vue({
  el: '#main',
  data() {
    const { search } = unserialize(window.location.hash)
    return {
      articles: [],
      lookup: {},
      searchFocus: false,
      filter: {
        search: search || ''
      }
    }
  },
  computed: {
    filtering () {
      return serialize(this.filter)
    }
  },
  methods: {
    async search() {
      if (!this.filtering) {
        this.articles = []
        return
      }
      this.articles = await wpFetch('wp/v2/bijdrage?' + this.filtering)
    },
    onFocus() {
      this.searchFocus = true
    }
  },
  async mounted() {
    this.search()
    const d = await wpFetch('hsn-theme/bijdrage-taxonomies')
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
  },
  watch: {
    filter: {
      deep: true,
      handler(s, o) {
        this.search()
        window.history.replaceState(this.filter, '', '#' + serialize(this.filter))
      }
    },
    lookup: {
      deep: true,
      handler (t) {
        const params = {}
        t.forEach(tax => {
          tax.name = tax.key.charAt(0).toUpperCase() + tax.key.slice(1).replace('_', ' ')
          tax.terms.forEach(term => {
            if (term.selected) {
              include(term.taxonomy, term.term_id)
            }
            if (term.terms && term.terms.length) {
              term.terms.forEach(sub => {
                if (sub.selected) {
                  include(sub.taxonomy, sub.term_id)
                }
              })
            }
          })
        })
        this.filter = Object.assign({
          search: this.filter.search
        }, params)
        function include(taxonomy, term_id) {
          if (params[taxonomy]) {
            params[taxonomy] += ',' + term_id
          } else {
            params[taxonomy] = '' + term_id
          }
        }
      }
    }
  }
})

function wpFetch(url) {
  let res
  return fetch(window.restUrl + url).then(r => {
    res = r
    return r.json()
  })
    .then(d => {
      if (d.code) {
        throw new Error('Fetch error: ' + d.code)
      }
      d.total = res.headers.get('X-WP-Total')
      return d
    })
}

function serialize(obj) {
  const str = []
  for (const p in obj) {
    if (obj.hasOwnProperty(p) && obj[p]) {
      str.push(encodeURIComponent(p) + '=' + encodeURIComponent(obj[p]))
    }
  }
  return str.join('&')
}

function unserialize(str) {
  const query = str[0] === '#' || str[0] === '?' ? str.slice(1) : str
  const result = {}
  query.split('&').forEach(part => {
    const item = part.split('=')
    result[decodeURIComponent(item[0])] = decodeURIComponent(item[1])
  })
  return result
}

// http://hsn.test/wp-json/wp/v2/bijdrage
