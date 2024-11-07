shader_type canvas_item;

uniform sampler2D screen_texture : hint_screen_texture, filter_nearest;

void fragment() {
	vec4 color = texture(screen_texture, SCREEN_UV);
	color = color * texture(TEXTURE, UV).a;
	COLOR = vec4(1.0 - color.rgb, color.a);
}
