import { TokenService } from "@/services";

export function currency() {
  return {
    position: TokenService.getCurrency().position ?? "before",
    symbol: TokenService.getCurrency().symbol ?? "Rp ",
  };
}
