shader_type canvas_item;

void fragment() {
	COLOR = texture(TEXTURE, UV); // Read from texture again.
	COLOR.rgb = vec3(0.0); //set blue channel to 1.0
}
