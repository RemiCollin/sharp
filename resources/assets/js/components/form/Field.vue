<script>
    import Fields, { NameAssociation as fieldCompName } from './fields/index';
    import * as util from '../../util';

    export default {
        name:'SharpField',
        components: Fields,

        provide() {
            return {
                $field: this
            }
        },

        props: {
            fieldKey: String,
            fieldType:  String,
            fieldProps: Object,
            fieldLayout: Object,
            value: [String, Number, Boolean, Object, Array],
            locale: String,
            uniqueIdentifier: String,
            updateData: Function
        },
        mounted() {
            //console.log(this);
        },
        render(h) {
            if(!(this.fieldType in fieldCompName)) {
                util.error(`SharpField '${this.fieldKey}', unknown type '${this.fieldType}'`, this.fieldProps);
                return null;
            }

            let { key, ...fieldProps } = this.fieldProps;

//            console.log({
//                fieldKey:this.fieldKey,
//                fieldLayout:this.fieldLayout,
//                value:this.value,
//                locale:this.locale,
//                uniqueIdentifier:this.uniqueIdentifier,
//                ...fieldProps
//            });

            return h(fieldCompName[this.fieldType],{
                props : {
                    fieldKey:this.fieldKey,
                    fieldLayout:this.fieldLayout,
                    value:this.value,
                    locale:this.locale,
                    uniqueIdentifier:this.uniqueIdentifier,
                    ...fieldProps
                },
                on: {
                    input: val => {
                        if(this.fieldProps.readOnly)
                            util.warn(`SharpField '${this.fieldKey}', can't update because is readOnly`);
                        else
                            this.updateData(this.fieldKey,val);
                    },
                    blur: _ => {
                        this.fieldProps.focused = false;
                    }
                }
            });
        }
    }
</script>