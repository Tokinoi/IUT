shader_type canvas_item;

uniform sampler2D curve;
uniform float strength : hint_range(-1.0, 1.0) = 0.5;
uniform sampler2D screen_texture : hint_screen_texture, filter_nearest;

void fragment() {
	vec2 vecToCenter = vec2(0.5, 0.5) - UV;
	float distToCenter = length(vecToCenter);
	float curveVal = texture(curve, vec2(distToCenter)).r;
	vec2 diff = normalize(vecToCenter) * strength * curveVal;
	COLOR = texture(screen_texture, SCREEN_UV - diff);
}