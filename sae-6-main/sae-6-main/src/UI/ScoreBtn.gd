extends LineEdit

func _ready():
	text = Config.scoreLimit
	
func _on_text_submitted(new_text):
	var result = new_text
	if result:
		text = result
	else:
		text = ""
	Config.scoreLimit = text
