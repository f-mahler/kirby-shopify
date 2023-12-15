<template>
      <div class="k-button-field-button-wrapper">
            <k-button v-if="!isLoading" :icon="icon" :theme="theme" variant="filled" @click="onClick">{{ text }}</k-button>
            <k-button v-if="isLoading && !hasError" icon="dots" variant="filled" disabled="true">Please wait</k-button>
            <k-button v-if="hasError" icon="alert" theme="negative" variant="filled">Error</k-button>
      </div>
  </template>
  
  <script>  
export default {
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
            async onClick(){
                  if( this.open === true ){
                        this.openUrlInNewTab( this );
                  } else {
                        this.triggerWebhook( this );
                  }
            },
            openUrlInNewTab( field ){
                  console.log('Button field', field.label, field.url);
                  window.open( field.url, '_blank' );
            },
            triggerWebhook(field) {
                  console.log('Button field', field.label, field.url);
                  field.isLoading = true;
                  fetch( field.url )
                        .then((response) => response.json())
                        .then((data) => {

                              field.isLoading = false;
                              console.log('Button field', 'Webhook successfully triggered', data);

                              console.log( field );
                              
                              if( field.reload === true ){
                              setTimeout(() => {
                                    field.$reload();
                              }, 50);
                              }

                        })
                        .catch((error) => {
                              field.hasError = true;
                              console.error('Button field', 'Error', error);
                        });
            }
      }
}
  </script>
  
  <style>
  
      p {
          margin-bottom: var(--spacing-2);
      }
  
      .k-button-field-button {
          display: block;
          width: 100%;
          text-align: left;
      }
  
  </style>