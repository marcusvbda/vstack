<html>
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/grapesjs/0.17.29/css/grapes.min.css">
      <script src="https://cdnjs.cloudflare.com/ajax/libs/grapesjs/0.17.29/grapes.min.js"></script>
      <style>
         .gjs-fonts::before {
            display: flex!important;
            align-items: center!important;
            justify-content: center!important;
         }
      </style>
   </head>
   <body style="margin:0;">
      <div id="gjs" style="height:0px; overflow:hidden;"></div>
      <script type="text/javascript">
         window.parent.setIframeWindowValue = (key, value) => {
           window[key] = value;
         }    
         
         window.grapesOptions = {}
         
         const startEditor = () => {
           let options = window.grapesOptions;
            options.container = '#gjs';
            options.height = '100%';
           const editor = grapesjs.init(window.grapesOptions);  


           window.grapesEditor = editor;
         }
         
         window.startEditor = startEditor;          
      </script>
   </body>
</html>