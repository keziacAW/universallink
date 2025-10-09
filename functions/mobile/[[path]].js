export async function onRequest(context) {
  const { request, env } = context;
  const url = new URL(request.url);

  // Serve the mobile/index.html for all /mobile/* paths
  const response = await env.ASSETS.fetch(new URL('/mobile/index.html', url.origin));

  return new Response(response.body, {
    status: 200,
    headers: response.headers
  });
}
