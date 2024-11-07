mod vec2d;
mod point;

#[cfg(test)]
mod tests {
    use crate::point::{point};
    use crate::vec2d::{Vec2D, vector_null};
    use crate::vec2d::unit_vector;

    #[test]
    fn test_null_vector() {
        let vector = vector_null();
        assert_eq!(vector.x, 0.0);
        assert_eq!(vector.y, 0.0);
    }

    #[test]
    fn test_unit_vector() {
        let vector = unit_vector();
        assert_eq!(vector.x, 1.0);
        assert_eq!(vector.y, 1.0);
    }

    #[test]
    fn test_move_point_with_null_vector() {
        let vector = vector_null();
        let mut point = point(1.0, 1.0);

        point.move_point(vector);

        assert_eq!(point.x, 1.0);
        assert_eq!(point.y, 1.0);
    }

    #[test]
    fn test_move_point_with_unit_vector() {
        let vector = unit_vector();
        let mut point = point(1.0, 1.0);

        point.move_point(vector);

        assert_eq!(point.x, 2.0);
        assert_eq!(point.y, 2.0);
    }

    #[test]
    fn test_norm_vector_null() {
        let vector = vector_null();
        let norm = vector.norm();

        assert_eq!(norm, 0.0)
    }

    #[test]
    fn test_norm_vector_unit() {
        let vector = unit_vector();
        let norm = vector.norm();

        assert_eq!(norm, std::f64::consts::SQRT_2)
    }

    #[test]
    fn test_norm_vector_most_famous_right_triangle() {
        let vector = Vec2D{ x: 3.0, y: 4.0 }; // 3, 4, 5
        let norm = vector.norm();

        assert_eq!(norm, 5.0)
    }

    #[test]
    fn test_homothety_on_vector_null() {
        let mut vector = vector_null();
        vector.homothety(10.0);

        assert_eq!(vector.x, 0.0);
        assert_eq!(vector.y, 0.0);
    }

    #[test]
    fn test_homothety_on_unit_vector() {
        let mut vector = unit_vector();
        vector.homothety(10.0);

        assert_eq!(vector.x, 10.0);
        assert_eq!(vector.y, 10.0);
    }

    #[test]
    fn test_homothety() {
        let mut vector = Vec2D{ x: 3.0, y: 4.0 };
        vector.homothety(10.0);

        assert_eq!(vector.x, 30.0);
        assert_eq!(vector.y, 40.0);
    }

    #[test]
    fn test_normalize_vector_null() {
        let mut vector = vector_null();
        vector.normalize();

        assert_eq!(vector.x, 0.0);
        assert_eq!(vector.y, 0.0);
    }

    #[test]
    fn test_normalize_unit_vector() {
        let mut vector = unit_vector();
        vector.normalize();

        assert_eq!(vector.x, std::f64::consts::FRAC_1_SQRT_2 - 0.0000000000000001); // CONSTANT APPROXIMATION ISSUE
        assert_eq!(vector.y, std::f64::consts::FRAC_1_SQRT_2 - 0.0000000000000001); // CONSTANT APPROXIMATION ISSUE
    }

    #[test]
    fn test_normalize() {
        let mut vector = Vec2D{ x: 3.0, y: 4.0 };
        vector.normalize();

        assert_eq!(vector.x, 0.6);
        assert_eq!(vector.y, 0.8);
    }
}
