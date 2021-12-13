const traitInputAttr = { placeholder: "eg. Text here" };

export default {
  assetManager: {
    addButton: "Adicionar imagem",
    inputPlh: "http://path/to/the/image.jpg",
    modalTitle: "Selecione uma imagem",
    uploadTitle: "Arraste os arquivos ou clique para fazer um upload",
  },
  // Here just as a reference, GrapesJS core doesn't contain any block,
  // so this should be omitted from other local files
  blockManager: {
    labels: {
      // 'block-id': 'Block Label',
    },
    categories: {
      // 'category-id': 'Category Label',
    },
  },
  domComponents: {
    names: {
      "": "Box",
      wrapper: "Body",
      text: "Text",
      comment: "Comment",
      image: "Image",
      video: "Video",
      label: "Label",
      link: "Link",
      map: "Map",
      tfoot: "Table foot",
      tbody: "Table body",
      thead: "Table head",
      table: "Table",
      row: "Table row",
      cell: "Table cell",
    },
  },
  deviceManager: {
    device: "Dispositívo",
    devices: {
      desktop: "Desktop",
      tablet: "Tablet",
      mobileLandscape: "Mobile Horizontal",
      mobilePortrait: "Mobile Vertical",
    },
  },
  panels: {
    buttons: {
      titles: {
        preview: "Pré visualização",
        fullscreen: "Fullscreen",
        "sw-visibility": "Ver componentes",
        "export-template": "Ver código",
        "open-sm": "Abrir gerenciador de estilos",
        "open-tm": "Configurações",
        "open-layers": "Abrir gerenciador de camadas",
        "open-blocks": "Abrir blocos",
      },
    },
  },
  selectorManager: {
    label: "Classes",
    selected: "Selected",
    emptyState: "- Estado -",
    states: {
      hover: "Hover",
      active: "Click",
      "nth-of-type(2n)": "Even/Odd",
    },
  },
  styleManager: {
    empty: "Seleciona um elemento",
    layer: "Camada",
    fileButton: "Imagens",
    sectors: {
      general: "Geral",
      layout: "Layout",
      typography: "Tipografia",
      decorations: "Decorações",
      extra: "Extra",
      flex: "Flex",
      dimension: "Dimensões",
    },
    // The core library generates the name by their `property` name
    properties: {
      // float: 'Float',
    },
  },
  traitManager: {
    empty: "Seleciona um elemento",
    label: "Configurações de componentes",
    traits: {
      // The core library generates the name by their `name` property
      labels: {
        // id: 'Id',
        // alt: 'Alt',
        // title: 'Title',
        // href: 'Href',
      },
      // In a simple trait, like text input, these are used on input attributes
      attributes: {
        id: traitInputAttr,
        alt: traitInputAttr,
        title: traitInputAttr,
        href: { placeholder: "eg. https://google.com" },
      },
      // In a trait like select, these are used to translate option names
      options: {
        target: {
          false: "Mesma janela",
          _blank: "Nova janela",
        },
      },
    },
  },
};
