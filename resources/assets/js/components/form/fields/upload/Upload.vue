<template>
    <sharp-vue-clip :options="options" :value="value" :ratioX="ratioX" :ratioY="ratioY"
                    :read-only="readOnly"
                    @error="$field.$emit('error',$event)"
                    @reset="$field.$emit('clear')">
    </sharp-vue-clip>
</template>

<script>
    import Vue from 'vue';
    import SharpVueClip from './VueClip';
    import Messages from '../../../../messages';

    import { UPLOAD_URL } from '../../../../consts';
    import { UploadXSRF } from '../../../../mixins';

    export default {
        name: 'SharpUpload',
        components: {
            SharpVueClip
        },

        mixins: [ UploadXSRF ],
        inject: [ '$field', 'xsrfToken' ],

        props: {
            value: Object,

            fileFilter: Array,
            maxFileSize: Number,
            thumbnail: String,

            ratioX:Number,
            ratioY:Number,

            readOnly: Boolean
        },
        computed: {
            options() {
                let opt = {};

                opt.url = UPLOAD_URL;
                opt.uploadMultiple = false;

                if (this.fileFilter) {
                    opt.acceptedFiles = {
                        extensions: this.fileFilter,
                        message: Messages.uploadFileBadExtension
                    }
                }
                if (this.maxFileSize) {
                    opt.maxFilesize = {
                        limit: this.maxFileSize,
                        message: Messages.uploadFileTooBig
                    }
                }
                this.patchXsrf(opt);
                return opt;
            }
        },
        methods:{
        }
    };
</script>