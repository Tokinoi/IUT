use crate::vec2d::Vec2D;

pub(crate) struct Point {
    pub(crate) x: f64,
    pub(crate) y: f64,
}

pub fn point(x: f64, y: f64) -> Point {
    Point { x, y }
}

impl Point {
    pub(crate) fn move_point(&mut self, _vector: Vec2D){
    self.x += _vector.x;
    self.y += _vector.y;
    }
}
