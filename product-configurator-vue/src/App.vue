<script setup lang="ts">
import { ref } from "vue";

const text = ref<string>("");
const amount = ref<number>(1);
const imageFile = ref<File | null>(null);
const imagePreview = ref<string | null>(null);

const handleImageUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (file) {
        imageFile.value = file;

        const reader = new FileReader();
        reader.onload = () => {
            imagePreview.value = reader.result as string;
        };

        reader.readAsDataURL(file);
    } else {
        imageFile.value = null;
        imagePreview.value = null;
    }
};

const submitToCart = async () => {
    const formData = new FormData();
    const testProductId = 13;

    formData.append("add-to-cart", testProductId.toString());
    formData.append("quantity", amount.value.toString());

    // Add additional custom fields (need to be handled in functions.php)
    formData.append("custom_text", text.value);

    if (imageFile.value) {
        formData.append("custom_image", imageFile.value);
    }

    for (const [key, value] of formData.entries()) {
        console.log(key, value);
    }

    try {
        const response = await fetch("/?add-to-cart=" + testProductId, {
            method: "POST",
            body: formData,
            credentials: "same-origin",
        });

        console.log("Product added!", response);
    } catch (err) {
        console.error("Failed to add to cart", err);
    }
};
</script>

<template>
    <div class="product-configurator">
        <div class="image">
            <p v-if="text">{{ text }}</p>
            <img v-if="imagePreview" :src="imagePreview" alt="Preview" />
            <p v-else>No image selected</p>
        </div>

        <div class="user-input">
            <form @submit.prevent="submitToCart">
                <label for="text">Text</label>
                <input type="text" name="custom_text" id="text" v-model="text" />

                <label for="image">Image</label>
                <input type="file" name="custom_image" id="image" @change="handleImageUpload" accept="image/*" />

                <label for="amount">Amount</label>
                <input type="number" name="amount" id="amount" v-model="amount" min="1" />

                <button class="btn" type="submit">Add to cart</button>
            </form>
        </div>
    </div>
</template>

<style scoped></style>
