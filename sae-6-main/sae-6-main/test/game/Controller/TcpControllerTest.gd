# GdUnit generated TestSuite
class_name TcpControllerAdapterTest
extends GdUnitTestSuite
@warning_ignore('unused_parameter')
@warning_ignore('return_value_discarded')

# TestSuite generated from
const __source = 'res://src/game/Controller/TCPController.gd'
var test_source = TCPControllerAdapter.new()

func test_get_trimmed_data() -> void:
	var expected = "NAME=TEST#COL=152=152=152"
	assert_str(test_source.get_trimmed_data("NAME=TEST#COL=152=152=152")).is_equal(expected)
	assert_str(test_source.get_trimmed_data("NAME=TEST#COL=152=152=152#\n")).is_equal(expected)
	assert_str(test_source.get_trimmed_data("NAME=TEST#COL=152=152=152#\n#\n#\n")).is_equal(expected)


func test_get_commands_string() -> void:
	assert_array(Array(test_source.get_commands_string("NAME=TEST#COL=152=152=152"))).is_equal(["NAME=TEST", "COL=152=152=152"])
	assert_array(Array(test_source.get_commands_string("NAME=TEST#COL=152=152=152#NLIST"))).is_equal(["NAME=TEST", "COL=152=152=152", "NLIST"])

func test_get_command_name() -> void:
	assert_str(test_source.get_command_name("NAME=TEST")).is_equal("name")
	assert_str(test_source.get_command_name("COL=152=152=152")).is_equal("col")
	assert_str(test_source.get_command_name("NLIST")).is_equal("nlist")


func test_get_command_args() -> void:
	assert_array(Array(test_source.get_command_args("NAME=TEST"))).is_equal(["TEST"])
	assert_array(Array(test_source.get_command_args("COL=152=152=152"))).is_equal(["152", "152", "152"])
	assert_array(Array(test_source.get_command_args("NLIST"))).is_equal([])


func test_to_formated_string() -> void:
	# VECTOR2
	assert_str(test_source.to_formated_string(Vector2(0, 0))).is_equal("0/0")
	assert_str(test_source.to_formated_string(Vector2(1, 1))).is_equal("1/1")
	assert_str(test_source.to_formated_string(Vector2(10, 10))).is_equal("10/10")
	assert_str(test_source.to_formated_string(Vector2(-10, -10))).is_equal("-10/-10")
	
	# INT
	assert_str(test_source.to_formated_string(0)).is_equal("0")
	assert_str(test_source.to_formated_string(10)).is_equal("10")
	assert_str(test_source.to_formated_string(-10)).is_equal("-10")
	
	# FLOAT
	assert_str(test_source.to_formated_string(0.0)).is_equal("0")
	assert_str(test_source.to_formated_string(10.0)).is_equal("10")
	assert_str(test_source.to_formated_string(10.10)).is_equal("10.1")
	assert_str(test_source.to_formated_string(-10.0)).is_equal("-10")
	assert_str(test_source.to_formated_string(-10.10)).is_equal("-10.1")
	assert_str(test_source.to_formated_string(-10.1234)).is_equal("-10.1")
	
	# STRING
	assert_str(test_source.to_formated_string("EMPTY")).is_equal("EMPTY")
	assert_str(test_source.to_formated_string("")).is_equal("")
	assert_str(test_source.to_formated_string("TEST")).is_equal("TEST")
	assert_str(test_source.to_formated_string("15")).is_equal("15")
	assert_str(test_source.to_formated_string("-10")).is_equal("-10")
	assert_str(test_source.to_formated_string("10.5")).is_equal("10.5")
	assert_str(test_source.to_formated_string("-10.5")).is_equal("-10.5")
	
	# ARRAY
	assert_str(test_source.to_formated_string([])).is_equal("")
	assert_str(test_source.to_formated_string(["0"])).is_equal("0")
	assert_str(test_source.to_formated_string(["0", "1"])).is_equal("0=1")
	assert_str(test_source.to_formated_string(["EMPTY", "EMPTY", "EMPTY"])).is_equal("EMPTY=EMPTY=EMPTY")
	assert_str(test_source.to_formated_string(["-10", "EMPTY", "10"])).is_equal("-10=EMPTY=10")
