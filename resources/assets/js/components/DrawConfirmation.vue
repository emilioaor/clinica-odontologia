<template>
    <section>
        <div>
            <p class="title">
                Firma
                <a class="pull-right" @click="close">X</a>
            </p>
            <canvas
                    id="canvas"
                    style="border: 1px solid black;"
                    @mousemove="move"
                    @touchmove="move"
            ></canvas>

            <div class="text-center">
                <button class="btn btn-warning" @click="drawing = true" v-if="! drawing">
                    <i class="glyphicon glyphicon-edit"></i>
                    Editar
                </button>

                <button class="btn btn-default" @click="drawing = false" v-if="drawing">
                    <i class="glyphicon glyphicon-stop"></i>
                    Detener
                </button>

                <button class="btn btn-danger" @click="clear">
                    <i class="glyphicon glyphicon-remove"></i>
                    Borrar
                </button>

                <button class="btn btn-success" :disabled="! ready || drawing" @click="generateBase64">
                    <i class="glyphicon glyphicon-ok"></i>
                    Guardar
                </button>
            </div>
        </div>
    </section>
</template>

<script>
    export default {
        data() {
            return {
                currX: null,
                currY: null,
                prevX: null,
                prevY: null,
                drawing: false,
                canvas: null,
                ctx: null,
                color: "black",
                ready: false
            }
        },
        mounted: function() {
            this.canvas = document.getElementById('canvas');
            this.ctx = canvas.getContext('2d');
            this.ctx.strokeStyle = this.color;
        },
        methods: {
            start: function() {
                this.drawing = true;
                this.ctx.moveTo(this.currX, this.currY);
            },
            move: function(event) {
                this.prevX = this.currX;
                this.prevY = this.currY;

                this.currX = event.offsetX ? event.offsetX : event.touches[0].pageX - event.touches[0].target.offsetLeft;
                this.currY = event.offsetY ? event.offsetY : event.touches[0].pageY - event.touches[0].target.offsetTop;

                if (this.drawing) {
                    this.ctx.lineTo(this.currX, this.currY);
                    this.ctx.stroke();
                    this.ready = true;
                }
            },
            clear: function() {
                this.currX = null;
                this.currY = null;
                this.prevX = null;
                this.prevY = null;
                this.drawing = false;
                this.ready = false;
                this.canvas.width = this.canvas.width;
            },
            generateBase64: function () {
                this.$emit('save-sign', {base64: this.canvas.toDataURL()})
            },
            close: function () {
                this.$emit('close')
            }
        }
    }
</script>

<style lang="scss">
    section {
        padding: 15px;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
        > div {
            background-color: #fff;
            padding: 15px;
        }
    }
    .title {
        font-weight: bold;
        font-size: 18px;
    }
</style>