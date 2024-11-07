pub(crate) struct Vec2D {
    pub(crate) x: f64,
    pub(crate) y: f64,
}

pub
fn vector_null() -> Vec2D {
    Vec2D { x: 0.0, y: 0.0 }
}

pub
fn unit_vector() -> Vec2D {
    Vec2D { x: 1.0, y: 1.0 }
}

impl Vec2D {
    pub(crate) fn norm(&self) -> f64 {
        f64::sqrt(
            f64::powi(self.x, 2) + f64::powi(self.y, 2)
        )
    }

    pub(crate) fn homothety(&mut self, ratio: f64) {
        self.x *= ratio;
        self.y *= ratio;
    }
    pub(crate) fn normalize(&mut self) {
        let norm = self.norm();

        // null vector cannot be normalize
        if (norm == 0.0) {
            return;
        }
        self.x /= norm;
        self.y /= norm;
    }

    pub(crate) fn rotate(&mut self, angle: f64) {
        self.x = f64::cos(angle * self.x) - f64::sin(angle * self.x);
        self.x = f64::cos(angle * self.x) + f64::sin(angle * self.x);
    }

    // pub(crate) fn scal_dot(&mut self, vector:Vec2D)
}