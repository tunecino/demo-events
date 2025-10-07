/**
 * Generates a deterministic UUID-like ID based on the browser's User-Agent.
 * Matches the output of the PHP version under api/app/Support/helpers.php:
 *
 *   sha1(User-Agent) formatted as 8-4-4-4-12
 */
export async function browserUserId(): Promise<string> {
  const agent = navigator.userAgent || "unknown";
  const hash = await sha1(agent);

  return [
    hash.substring(0, 8),
    hash.substring(8, 12),
    hash.substring(12, 16),
    hash.substring(16, 20),
    hash.substring(20, 32),
  ].join("-");
}

/**
 * Computes SHA-1 hash of a string and returns a lowercase hex string.
 */
async function sha1(str: string): Promise<string> {
  const data = new TextEncoder().encode(str);
  const buffer = await crypto.subtle.digest("SHA-1", data);
  const hashArray = Array.from(new Uint8Array(buffer));
  return hashArray.map((b) => b.toString(16).padStart(2, "0")).join("");
}
