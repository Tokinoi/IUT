shader_type spatial;

uniform sampler2D explosionTexture;

void vertex() {
	VERTEX.y = VERTEX.y + sin(TIME);
}

void fragment() {
	vec2 uv = UV;
	uv.y += uv.x + sin(TIME * 5.0) * 0.001;
	vec3 texture_color = texture(explosionTexture, uv).rgb;
	ALBEDO = texture_color;
}
