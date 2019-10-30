Vue.config.productionTip = false
Vue.config.devtools = false

const app = new Vue({
  el: '#main',
  data() {
    const search = unserialize(window.location.search).q || ''
    const filter = unserialize(window.location.hash)
    return {
      articles: [],
      lookup: {},
      numberLookup: {},
      numberLoading: [],
      searchFocus: false,
      filter: {
        category: '',
        post_tag: '',
        nav_menu: '',
        link_category: '',
        post_format: '',
        aantal_respondenten: '',
        doelgroep: '',
        domein: '',
        land: '',
        leeftijd: '',
        dataverzameling: '',
        onderwijstype: '',
        respondenten: '',
        tekstsoort: '',
        thema: '',
        search,
        ...filter
      }
    }
  },
  computed: {
    filtering() {
      return serialize(this.filter)
    },
    hasMore() {
      return this.articles.total > this.articles.length
    }
  },
  methods: {
    async search() {
      if (!this.filtering) {
        this.articles = []
        return
      }
      this.articles = await wpFetch('wp/v2/bijdrage?' + this.filtering)
      this.loadNumbers(this.articles.map(a => a.id))
    },
    async loadMore() {
      const offset = '&offset=' + this.articles.length
      const page = await wpFetch('wp/v2/bijdrage?' + this.filtering + offset)
      page.forEach(a => this.articles.push(a))
      this.loadNumbers(page.map(a => a.id))
    },
    async loadNumbers(ids) {
      const toLoad = ids.filter(id => !this.numberLoading.includes(id))
      if (!toLoad.length) {
        return
      }
      this.numberLoading = this.numberLoading.concat(toLoad)
      const numbers = await wpFetch('hsn-theme/homepage-search?posts=' + toLoad)
      numbers.forEach(num => this.numberLookup[num.id] = num.bundelnummer)
      this.numberLookup = Object.assign({}, this.numberLookup)
    },
    onFocus() {
      this.searchFocus = true
    }
  },
  async mounted() {
    if (window.location.hash.length > 4) {
      // this.search()
    }
    const d = await wpFetch('hsn-theme/bijdrage-taxonomies')
    if (!d || !d.forEach) {
      return console.warn('API error', d)
    }
    const { filter } = this
    d.forEach(tax => {
      tax.open = filter[tax.key]
      tax.terms.forEach(term => {
        term.open = false
        term.selected = isSelected(term)
        if (term.terms && term.terms.length) {
          term.terms.forEach(sub => {
            sub.selected = isSelected(sub)
            if (sub.selected) {
              term.open = true
            }
          })
          term.subs = term.terms.length
        }
      })
    })
    this.lookup = d

    function isSelected(term) {
      const val = filter[term.taxonomy]
      return val && val.includes(term.term_id)
    }
  },
  watch: {
    filter: {
      deep: true,
      handler(s, o) {
        this.search()

        const old = String(window.location.hash).replace('#', '')
        const query = serialize(this.filter)
        if (old !== query) {
          window.history.replaceState(
            this.filter,
            '',
            query ? '#' + query : ''
          )
        }
      }
    },
    lookup: {
      deep: true,
      handler(t) {
        const params = {}
        t.forEach(tax => {
          tax.name =
            tax.key.charAt(0).toUpperCase() + tax.key.slice(1).replace('_', ' ')
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
        this.filter = Object.assign(
          {
            search: this.filter.search
          },
          params
        )
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
  return fetch(window.restUrl + url)
    .then(r => {
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
  query.split('&').filter(Boolean).forEach(part => {
    const item = part.split('=')
    result[decodeURIComponent(item[0])] = decodeURIComponent(item[1])
  })
  return result
}

// http://hsn.test/wp-json/wp/v2/bijdrage
