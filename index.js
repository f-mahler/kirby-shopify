(function() {
  "use strict";
  const Button_vue_vue_type_style_index_0_lang = "";
  function normalizeComponent(scriptExports, render, staticRenderFns, functionalTemplate, injectStyles, scopeId, moduleIdentifier, shadowMode) {
    var options = typeof scriptExports === "function" ? scriptExports.options : scriptExports;
    if (render) {
      options.render = render;
      options.staticRenderFns = staticRenderFns;
      options._compiled = true;
    }
    if (functionalTemplate) {
      options.functional = true;
    }
    if (scopeId) {
      options._scopeId = "data-v-" + scopeId;
    }
    var hook;
    if (moduleIdentifier) {
      hook = function(context) {
        context = context || // cached call
        this.$vnode && this.$vnode.ssrContext || // stateful
        this.parent && this.parent.$vnode && this.parent.$vnode.ssrContext;
        if (!context && typeof __VUE_SSR_CONTEXT__ !== "undefined") {
          context = __VUE_SSR_CONTEXT__;
        }
        if (injectStyles) {
          injectStyles.call(this, context);
        }
        if (context && context._registeredComponents) {
          context._registeredComponents.add(moduleIdentifier);
        }
      };
      options._ssrRegister = hook;
    } else if (injectStyles) {
      hook = shadowMode ? function() {
        injectStyles.call(
          this,
          (options.functional ? this.parent : this).$root.$options.shadowRoot
        );
      } : injectStyles;
    }
    if (hook) {
      if (options.functional) {
        options._injectStyles = hook;
        var originalRender = options.render;
        options.render = function renderWithStyleInjection(h, context) {
          hook.call(context);
          return originalRender(h, context);
        };
      } else {
        var existing = options.beforeCreate;
        options.beforeCreate = existing ? [].concat(existing, hook) : [hook];
      }
    }
    return {
      exports: scriptExports,
      options
    };
  }
  const _sfc_main = {
    props: {
      label: String,
      text: String,
      url: String,
      theme: String,
      icon: String,
      open: Boolean,
      reload: Boolean,
      help: String,
      isLoading: true,
      hasError: false
    },
    methods: {
      async onClick() {
        if (this.open === true) {
          this.openUrlInNewTab(this);
        } else {
          this.triggerWebhook(this);
        }
      },
      openUrlInNewTab(field) {
        console.log("Button field", field.label, field.url);
        window.open(field.url, "_blank");
      },
      triggerWebhook(field) {
        console.log("Button field", field.label, field.url);
        field.isLoading = true;
        fetch(field.url).then((response) => response.json()).then((data) => {
          field.isLoading = false;
          console.log("Button field", "Webhook successfully triggered", data);
          console.log(field);
          if (field.reload === true) {
            setTimeout(() => {
              field.$reload();
            }, 50);
          }
        }).catch((error) => {
          field.hasError = true;
          console.error("Button field", "Error", error);
        });
      }
    }
  };
  var _sfc_render = function render() {
    var _vm = this, _c = _vm._self._c;
    return _c("div", { staticClass: "k-button-field-button-wrapper" }, [!_vm.isLoading ? _c("k-button", { attrs: { "icon": _vm.icon, "theme": _vm.theme, "variant": "filled" }, on: { "click": _vm.onClick } }, [_vm._v(_vm._s(_vm.text))]) : _vm._e(), _vm.isLoading && !_vm.hasError ? _c("k-button", { attrs: { "icon": "dots", "variant": "filled", "disabled": "true" } }, [_vm._v("Please wait")]) : _vm._e(), _vm.hasError ? _c("k-button", { attrs: { "icon": "alert", "theme": "negative", "variant": "filled" } }, [_vm._v("Error")]) : _vm._e()], 1);
  };
  var _sfc_staticRenderFns = [];
  _sfc_render._withStripped = true;
  var __component__ = /* @__PURE__ */ normalizeComponent(
    _sfc_main,
    _sfc_render,
    _sfc_staticRenderFns,
    false,
    null,
    null,
    null,
    null
  );
  __component__.options.__file = "/Users/fm-a/dev/expe/site/plugins/kirby-shopify/src/fields/Button.vue";
  const Button = __component__.exports;
  panel.plugin("f-mahler/kirby-shopify", {
    fields: {
      button: Button
    }
  });
})();
