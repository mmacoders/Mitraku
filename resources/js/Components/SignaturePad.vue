<template>
  <div class="signature-pad">
    <canvas 
      ref="canvas" 
      class="border border-gray-300 rounded-md"
      :width="width" 
      :height="height"
      @mousedown="startDrawing"
      @mousemove="draw"
      @mouseup="stopDrawing"
      @mouseleave="stopDrawing"
    ></canvas>
    <div class="mt-2 flex space-x-2">
      <button 
        type="button" 
        @click="clearCanvas"
        class="px-3 py-1 text-sm bg-gray-200 hover:bg-gray-300 rounded-md"
      >
        Clear
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  width: {
    type: Number,
    default: 400
  },
  height: {
    type: Number,
    default: 200
  }
})

const canvas = ref(null)
const isDrawing = ref(false)
let ctx = null

defineExpose({
  getSignature: () => {
    if (canvas.value) {
      return canvas.value.toDataURL()
    }
    return null
  },
  clear: clearCanvas
})

onMounted(() => {
  if (canvas.value) {
    ctx = canvas.value.getContext('2d')
    ctx.lineWidth = 2
    ctx.lineCap = 'round'
    ctx.strokeStyle = '#000000'
    clearCanvas()
  }
})

const startDrawing = (e) => {
  isDrawing.value = true
  draw(e)
}

const draw = (e) => {
  if (!isDrawing.value) return
  
  const rect = canvas.value.getBoundingClientRect()
  const x = e.clientX - rect.left
  const y = e.clientY - rect.top
  
  ctx.lineTo(x, y)
  ctx.stroke()
  ctx.beginPath()
  ctx.moveTo(x, y)
}

const stopDrawing = () => {
  isDrawing.value = false
  ctx.beginPath()
}

const clearCanvas = () => {
  if (ctx && canvas.value) {
    ctx.fillStyle = '#ffffff'
    ctx.fillRect(0, 0, canvas.value.width, canvas.value.height)
    ctx.fillStyle = '#000000'
  }
}

onUnmounted(() => {
  isDrawing.value = false
})
</script>

<style scoped>
.signature-pad {
  display: inline-block;
}
</style>