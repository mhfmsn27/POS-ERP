import { TokenService } from "@/services";

export function timezone_system() {
  return TokenService.getTimezone();
}
