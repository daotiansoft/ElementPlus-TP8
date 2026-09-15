<template>
  <div style="padding: 10px">
    <textarea :id="editorId"></textarea>
  </div>
</template>
<script setup>
import { ref, watch, onMounted, onBeforeUnmount } from "vue";
import user from "@/api/user";

const props = defineProps({
  modelValue: { type: String, default: "" },
  height: { type: Number, default: 400 },
});

const emit = defineEmits(["update:modelValue"]);

const editorId = ref(`mce-${Math.random().toString().slice(2)}`);
let editor = null;
const languageTypeList = {
  zh: "zh_CN",
  en: "en",
};

const loadTinyMCE = () => {
  return new Promise((resolve) => {
    if (window.tinymce) return resolve();

    const script = document.createElement("script");
    script.src = "./tinymce/tinymce.min.js";
    script.onload = () => {
      const theme = document.createElement("script");
      theme.src = "./tinymce/themes/inlite/theme.min.js";
      theme.onload = () => {
        // 加载语言包
        const langScript = document.createElement("script");
        langScript.src = `/tinymce/langs/${languageTypeList["zh"]}.js`;
        langScript.async = true;
        langScript.onload = () => {
          resolve(window.tinymce);
        };
        langScript.onerror = () => {
          resolve(window.tinymce);
        };
        document.head.appendChild(langScript);
      };
      document.head.appendChild(theme);
    };
    document.head.appendChild(script);
  });
};

onMounted(async () => {
  await loadTinyMCE();
  initEditor();
});
const initEditor = () => {
  window.tinymce.init({
    selector: `#${editorId.value}`,
    height: props.height,
    width: "100%",
    menubar: false,
    plugins:
      "advlist anchor autolink autosave code codesample colorpicker colorpicker contextmenu directionality emoticons fullscreen hr image imagetools insertdatetime link lists media nonbreaking noneditable pagebreak paste preview print save searchreplace spellchecker tabfocus table template textcolor textpattern   ",
    toolbar:
      "searchreplace bold italic underline strikethrough alignleft aligncenter alignright outdent indent blockquote undo redo removeformat subscript superscript code codesample hr bullist numlist link image charmap preview anchor pagebreak  media table forecolor backcolor fullscreen code ",
    toolbar_mode: "wrap",
    skin_url: "./tinymce/skins/lightgray",
    content_css: "./tinymce/skins/lightgray/content.min.css",
    maxImageSize: { type: Number, default: 10 * 1024 * 1024 }, // 10MB
    images_upload_handler: imageUploadHandler,
    allowedImageTypes: {
      type: Array,
      default: () => [
        "image/jpeg",
        "image/png",
        "image/gif",
        "image/webp",
        "image/bmp",
      ],
    },
    setup: (ed) => {
      editor = ed;
      ed.on("init", () => props.modelValue && ed.setContent(props.modelValue));
      ed.on("Change", () => emit("update:modelValue", ed.getContent()));
    },
  });
};

watch(
  () => props.modelValue,
  (val) => {
    if (editor && val !== editor.getContent()) {
      editor.setContent(val || "");
    }
  },
);

onBeforeUnmount(() => {
  if (editor) {
    editor.destroy();
    editor = null;
  }
});

const imageUploadHandler = (blobInfo, success, failure, progress) => {
  return new Promise(async (resolve, reject) => {
    const file = blobInfo.blob();
    const filename = blobInfo.filename();
    try {
      user
        .uploadFile({ image: file })
        .then((data) => {
          const imageUrl = data?.url || "";
          const editor = getEditorInstance();
          success(imageUrl);
        })
        .catch((error) => {
          failure(error.msg || error);
        });
    } catch (error) {
      reject(error.message || error);
    }
  });
};

const getEditorInstance = () => {
  if (window.tinymce) {
    return window.tinymce.get(editorId.value);
  }
  return null;
};

defineExpose({
  getContent: () => editor?.getContent() || "",
  setContent: (html) => editor?.setContent(html),
});
</script>
